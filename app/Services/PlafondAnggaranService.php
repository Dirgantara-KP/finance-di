<?php

namespace App\Services;

use App\Dtos\PlafondAnggaranDto;
use App\Exceptions\DuplicateTransactionException;
use App\Exceptions\ForbiddenActionException;
use App\Exceptions\InvalidPeriodException;
use App\Models\TmbdgtPlafond;
use App\Models\Vpon;
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

        $record = $this->repo->findExisting($this->lookupDto($f, $cOrgContr, $pon));
        if ($record !== null) {
            $monthly = $this->repo->monthlyState($record);
            $saldoAwal = $monthly['saldoAwal'];   // v_bdgt_saldomonth = Saldo Awal
            $addMonth = array_fill(0, 12, 0);     // Buffer penambahan baru (0 setelah load)
            $saldoAkhir = $saldoAwal;             // Saldo Akhir awal = Saldo Awal + 0
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
            'stat' => $record?->c_bdgt_stat,
            'canUpdate' => $record !== null && $this->canUpdate((string) $f['tahun'], (string) $record->c_bdgt_contrstat, (string) $record->c_bdgt_stat),
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

        if ($this->repo->findExisting($this->lookupDto($data, $cOrgContr, $pon))) {
            throw new DuplicateTransactionException(reference: "{$data['org']}/{$data['sandi']}/{$data['pon']}");
        }

        $inputAddMonth = $this->normalizeMonthly($data['add_month']);
        // Kondisi 1: saldo awal == null -> Saldo Penambahan menjadi v_bdgt_addmonth dan v_bdgt_saldomonth
        $saldoMonth = $inputAddMonth;
        $addMonth = $inputAddMonth;

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

            if ($record->c_bdgt_stat === 'CLS') {
                throw new ForbiddenActionException(action: 'update plafond (Plafond berstatus CLOSE tidak dapat diubah)');
            }

            if (! $this->canUpdate((string) $record->c_bdgt_anggaran, (string) $record->c_bdgt_contrstat, (string) $record->c_bdgt_stat)) {
                throw new ForbiddenActionException(action: 'update plafond (tahun lampau hanya view)');
            }

            $inputAddMonth = $this->normalizeMonthly($addMonth);

            $current = $this->repo->monthlyState($record);
            $curSaldoAwal = $current['saldoAwal']; // v_bdgt_saldomonth lama
            $newSaldoAwal = [];
            $newAddMonth = [];

            for ($i = 0; $i < 12; $i++) {
                $add = max(0, $inputAddMonth[$i] ?? 0);
                $curAwal = max(0, $curSaldoAwal[$i] ?? 0);

                // Kondisi 2: saldo awal != null:
                // 1. Saldo Penambahan menjadi v_bdgt_addmonth (replace value kondisi pertama)
                // 2. v_bdgt_saldomonth = Saldo Awal lama + Saldo Penambahan baru
                $newAddMonth[$i] = $add;
                $newSaldoAwal[$i] = $curAwal + $add;
            }

            $this->repo->updateRecord($record, new PlafondAnggaranDto(
                saldoMonth: $newSaldoAwal,
                addMonth: $newAddMonth,
            ));
        });
    }

    public function setStat(int $id, string $stat): void
    {
        if (! in_array($stat, ['OPN', 'CLS'], true)) {
            throw new ForbiddenActionException(action: 'set status plafond (nilai tidak valid)');
        }

        DB::transaction(function () use ($id, $stat) {
            $record = $this->repo->findForUpdate($id);
            if (! $record) {
                throw new ForbiddenActionException(action: 'set status plafond (record tidak ditemukan)');
            }

            $this->repo->updateStat($id, $stat);
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
        $standard = range($currentYear, $currentYear - 5);
        $dbYears = TmbdgtPlafond::query()
            ->distinct()
            ->pluck('c_bdgt_anggaran')
            ->filter()
            ->map(fn ($y) => (int) $y)
            ->toArray();

        $allYears = array_unique(array_merge($standard, $dbYears));
        rsort($allYears);

        return array_combine($allYears, $allYears);
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
    private function lookupDto(array $f, string $orgContr, ?Vpon $pon): PlafondAnggaranDto
    {
        return new PlafondAnggaranDto(
            tahun: (string) $f['tahun'],
            org: $f['org'],
            sandi: $f['sandi'],
            pon: $f['pon'],
            orgContr: $orgContr,
            iContr: $f['kontrak'],
            pgm: $pon?->c_pgm ?? '',
            pgmSub: $pon?->c_pgm_sub ?? '',
        );
    }

    private function canUpdate(string $tahun, string $contrStat, string $bdgtStat = 'OPN'): bool
    {
        // c_bdgt_stat = CLS -> plafond dikunci, tidak bisa diupdate
        return (int) $tahun === now()->year && $contrStat === 'A3' && $bdgtStat !== 'CLS';
    }

    private function resolveActorNik(): string
    {
        return '900293';
    }
}
