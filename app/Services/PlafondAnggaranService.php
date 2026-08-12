<?php

namespace App\Services;

use App\Dtos\PlafondAnggaranDto;
use App\Exceptions\DuplicateTransactionException;
use App\Exceptions\ForbiddenActionException;
use App\Exceptions\InvalidPeriodException;
use App\Repositories\TmbdgtPlafondRepository;
use App\Repositories\TmcontrRepository;
use App\Repositories\TrchartacctRepository;
use App\Repositories\VororgRepository;
use App\Repositories\VponRepository;
use Illuminate\Support\Facades\DB;

final class PlafondAnggaranService
{
    public function __construct(
        private readonly TmbdgtPlafondRepository $repo,
        private readonly VororgRepository $orgRepo,
        private readonly TrchartacctRepository $sandiRepo,
        private readonly VponRepository $ponRepo,
        private readonly TmcontrRepository $kontrakRepo,
    ) {}

    /**
     * @param  array<string,mixed>  $f
     * @return array<string,mixed>
     */
    public function load(array $f): array
    {
        $pon = $this->ponRepo->findByVersion($f['pon']);
        $sandi = $this->sandiRepo->findByCost($f['sandi']);
        $kontrak = $this->kontrakRepo->findByContr($f['kontrak'], $f['org']);
        $cOrgContr = $kontrak->c_org_contr ?? $f['org'];

        $record = $this->repo->findExisting($this->lookupDto($f, $cOrgContr));
        if ($record !== null) {
            $monthly = $this->repo->monthlyState($record);
            $saldoAkhirDb = $monthly['saldoAwal'];
            $cumulativeAdd = $monthly['addMonth'];
            $saldoAwal = array_map(
                static fn (int $akhir, int $add): int => max(0, $akhir - $add),
                $saldoAkhirDb,
                $cumulativeAdd,
            );
            $saldoAkhir = $saldoAkhirDb;
            $addMonth = array_fill(0, 12, 0);
        } else {
            $saldoAwal = array_fill(0, 12, 0);
            $addMonth = array_fill(0, 12, 0);
            $saldoAkhir = array_fill(0, 12, 0);
        }

        return [
            'cPgm' => $pon?->c_pgm,
            'cPgmSub' => $pon?->c_pgm_sub,
            'namaProgram' => $pon->e_pgm ?? '',
            'namaSandi' => $sandi->e_cost ?? '',
            'cOrgContr' => $cOrgContr,
            'existingId' => $record?->id,
            'canUpdate' => $record !== null && $this->canUpdate((string) $f['tahun'], (string) $record->c_bdgt_contrstat),
            'saldoAwal' => $saldoAwal,
            'addMonth' => $addMonth,
            'saldoAkhir' => $saldoAkhir,
            'dataLoaded' => true,
        ];
    }

    /**
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    public function insert(array $data): array
    {
        $actorNik = $this->resolveActorNik();

        if ((int) $data['tahun'] !== now()->year) {
            throw new InvalidPeriodException(period: 'tahun '.$data['tahun']);
        }

        $pon = $this->ponRepo->findByVersion($data['pon']);

        if (! $pon) {
            throw new ForbiddenActionException(action: 'insert plafond (PON tidak ditemukan)');
        }

        $kontrak = $this->kontrakRepo->findByContr($data['kontrak'], $data['org']);
        $cOrgContr = $kontrak->c_org_contr ?? $data['org'];

        if ($this->repo->findExisting($this->lookupDto($data, $cOrgContr))) {
            throw new DuplicateTransactionException(reference: "{$data['org']}/{$data['sandi']}/{$data['pon']}");
        }

        $addMonth = $this->normalizeMonthly($data['add_month']);
        $saldoMonth = $addMonth;
        $addMonth = array_fill(0, 12, 0);

        $payload = new PlafondAnggaranDto(
            tahun: (string) $data['tahun'],
            org: $data['org'],
            sandi: $data['sandi'],
            pon: $data['pon'],
            orgContr: $cOrgContr,
            iContr: $data['kontrak'],
            saldoMonth: $saldoMonth,
            addMonth: $addMonth,
            pgm: $pon->c_pgm,
            pgmSub: $pon->c_pgm_sub,
            entry: $actorNik,
            orgCenter: $cOrgContr,
        );

        DB::transaction(fn () => $this->repo->insertRecord($payload));

        return [];
    }

    /**
     * @param  array<int,mixed>  $addMonth
     */
    public function update(int $id, array $addMonth): void
    {
        DB::transaction(function () use ($id, $addMonth) {
            $record = $this->repo->findForUpdate($id);
            if (! $record) {
                throw new ForbiddenActionException(action: 'update plafond (record tidak ditemukan)');
            }
            if (! $this->canUpdate((string) $record->c_bdgt_anggaran, (string) $record->c_bdgt_contrstat)) {
                throw new ForbiddenActionException(action: 'update plafond (tahun lampau hanya view)');
            }

            $addMonth = $this->normalizeMonthly($addMonth);

            $current = $this->repo->monthlyState($record);
            $curSaldoAkhir = $current['saldoAwal'];
            $curCumulativeAdd = $current['addMonth'];
            $newSaldoAkhir = [];
            $newCumulativeAdd = [];

            for ($i = 0; $i < 12; $i++) {
                $awal = max(0, ($curSaldoAkhir[$i] ?? 0) - ($curCumulativeAdd[$i] ?? 0));
                $add = max(0, $addMonth[$i] ?? 0);

                if ($awal === 0) {
                    $newSaldoAkhir[$i] = $add;
                    $newCumulativeAdd[$i] = 0;
                } else {
                    $newSaldoAkhir[$i] = $curSaldoAkhir[$i] + $add;
                    $newCumulativeAdd[$i] = $curCumulativeAdd[$i] + $add;
                }
            }

            $this->repo->updateRecord($record, new PlafondAnggaranDto(
                saldoMonth: $newSaldoAkhir,
                addMonth: $newCumulativeAdd,
            ));
        });
    }

    /**
     * @param  array<int,mixed>  $saldoAwal  @return array<int, int> * @param array<int,mixed> $addMonth
     */
    private function buildSaldoAkhir(array $saldoAwal, array $addMonth): array
    {
        return array_map(
            static fn (int $awal, int $add): int => max(0, $awal) + max(0, $add),
            $this->normalizeMonthly($saldoAwal),
            $this->normalizeMonthly($addMonth),
        );
    }

    public function getDropdownOptions(?string $org = null): array
    {
        return [
            'tahun' => $this->getTahunOptions(),
            'organisasi' => $this->getOrganisasiOptions(),
            'sandi' => $this->getSandiOptions(),
            'pon' => $this->getPonOptions(),
            'kontrak' => $this->getKontrakOptions($org),
        ];
    }

    /** @return array<int, int> */
    public function getTahunOptions(): array
    {
        $currentYear = now()->year;

        return array_combine(
            range($currentYear, $currentYear - 5),
            range($currentYear, $currentYear - 5)
        );
    }

    /**
     * @return array<string,string>
     */
    public function getOrganisasiOptions(): array
    {
        return $this->orgRepo->optionsForDropdown();
    }

    /**
     * @return array<string,string>
     */
    public function getSandiOptions(): array
    {
        return $this->sandiRepo->optionsForDropdown();
    }

    /**
     * @return array<string,string>
     */
    public function getPonOptions(): array
    {
        return $this->ponRepo->optionsForDropdown();
    }

    /**
     * @return array|array<string,string>
     */
    public function getKontrakOptions(?string $org): array
    {
        if (! $org) {
            return [];
        }

        return $this->kontrakRepo->kontrakOptionsFor($org);
    }

    /**
     * @param  array<int,mixed>  $saldoAwal  @return array{addMonth: array<int, int>, saldoAkhir: array<int, int>, totalSaldoAwal: int, totalPenambahan: int, totalSaldoAkhir: int} * @param array<int,mixed> $addMonth
     */
    public function calculateAll(array $saldoAwal, array $addMonth): array
    {
        $saldoAwal = $this->normalizeMonthly($saldoAwal);
        $addMonth = $this->normalizeMonthly($addMonth);
        $saldoAkhir = $this->buildSaldoAkhir($saldoAwal, $addMonth);

        return [
            'addMonth' => $addMonth,
            'saldoAkhir' => $saldoAkhir,
            'totalSaldoAwal' => array_sum($saldoAwal),
            'totalPenambahan' => array_sum($addMonth),
            'totalSaldoAkhir' => array_sum($saldoAkhir),
        ];
    }

    /** @param  array<int, mixed>  $monthly
     * @return array<int, int>
     */
    private function normalizeMonthly(array $monthly): array
    {
        $normalized = array_fill(0, 12, 0);
        foreach ($monthly as $i => $value) {
            if ($i > 11) {
                break;
            }
            $normalized[$i] = max(0, (int) ($value ?? 0));
        }

        return $normalized;
    }

    /** @param  array{tahun: int|string, org: string, sandi: string, pon: string, kontrak: string}  $f */
    private function lookupDto(array $f, string $orgContr): PlafondAnggaranDto
    {
        return new PlafondAnggaranDto(
            tahun: (string) $f['tahun'],
            org: $f['org'],
            sandi: $f['sandi'],
            pon: $f['pon'],
            orgContr: $orgContr,
            iContr: $f['kontrak'],
        );
    }

    private function canUpdate(string $tahun, string $contrStat): bool
    {
        return (int) $tahun === now()->year && $contrStat === 'A3';
    }

    private function resolveActorNik(): string
    {
        return '900293';
    }
}
