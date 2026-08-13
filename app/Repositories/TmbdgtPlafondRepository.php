<?php

namespace App\Repositories;

use App\Dtos\PlafondAnggaranDto;
use App\Models\TmbdgtPlafond;

final class TmbdgtPlafondRepository
{
    public function findForUpdate(int $id): ?TmbdgtPlafond
    {
        return TmbdgtPlafond::query()->whereKey($id)->lockForUpdate()->first();
    }

    public function findExisting(PlafondAnggaranDto $p): ?TmbdgtPlafond
    {
        return TmbdgtPlafond::query()
            ->where('c_bdgt_anggaran', $p->tahun)
            ->where('c_org', 'LIKE', $p->org.'%')
            ->where('c_pgm_ver', $p->pon)
            ->where('c_coa_dr', $p->sandi)
            ->forKontrak($p->orgContr, $p->iContr)
            ->first();
    }

    /** @return array{saldoAkhir: array<int, int>, addMonth: array<int, int>} */
    public function monthlyState(TmbdgtPlafond $record): array
    {
        $saldoAkhir = $addMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $saldoAkhir[$i - 1] = (int) ($record->{'v_bdgt_saldomonth'.$i} ?? 0);
            $addMonth[$i - 1] = (int) ($record->{'v_bdgt_addmonth'.$i} ?? 0);
        }

        return ['saldoAkhir' => $saldoAkhir, 'addMonth' => $addMonth];
    }

    public function insertRecord(PlafondAnggaranDto $p): TmbdgtPlafond
    {
        $data = array_merge(
            $this->buildHeader($p),
            $this->buildSaldoMonthRows($p->saldoMonth),
            $this->buildAddMonthRows($p->addMonth),
            [
                // spec: SALDOADDTOTAL = sum(SALDO) = sum(saldoMonth)
                // spec: SALDOTOTAL = sum(SALDOADD) = sum(addMonth)
                'v_bdgt_addtotal' => $this->sumMonthly($p->saldoMonth),
                'v_bdgt_saldototal' => $this->sumMonthly($p->addMonth),
            ],
        );

        return TmbdgtPlafond::query()->create($data);
    }

    public function updateRecord(TmbdgtPlafond $record, PlafondAnggaranDto $p): bool
    {
        return $record->update(array_merge(
            $this->buildSaldoMonthRows($p->saldoMonth),
            $this->buildAddMonthRows($p->addMonth),
            [
                // spec: SALDOADDTOTAL = sum(SALDO) = sum(saldoMonth)
                // spec: SALDOTOTAL = sum(SALDOADD) = sum(addMonth)
                'v_bdgt_addtotal' => $this->sumMonthly($p->saldoMonth),
                'v_bdgt_saldototal' => $this->sumMonthly($p->addMonth),
            ],
        ));
    }

    /** @return array<string, mixed> */
    private function buildHeader(PlafondAnggaranDto $p): array
    {
        return [
            'c_source' => 'COL',
            'c_org_id' => 'CO',
            'c_org' => $p->org,
            'c_org_contr' => $p->orgContr,
            'i_contr' => $p->iContr,
            'c_bdgt_contrstat' => 'A3',
            'c_bdgt_contrinex' => 'I',
            'c_bdgt_anggaran' => $p->tahun,
            'c_pgm' => $p->pgm,
            'c_pgm_sub' => $p->pgmSub,
            'c_pgm_ver' => $p->pon,
            'c_coa_dr' => $p->sandi,
            'c_coa_cr' => 'A23',
            'c_cy' => 'IDR',
            'i_entry' => $p->entry,
            'd_entry' => now(),
            'c_org_center' => $p->orgCenter,
        ];
    }

    /** @param  array<int, int>  $monthly */
    private function sumMonthly(array $monthly): int
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += max(0, (int) ($monthly[$i] ?? 0));
        }

        return $sum;
    }

    /** @param  array<int, int>  $saldoMonth
     * @return array<string, int>
     */
    private function buildSaldoMonthRows(array $saldoMonth): array
    {
        $rows = [];
        for ($i = 0; $i < 12; $i++) {
            $rows['v_bdgt_saldomonth'.($i + 1)] = max(0, (int) ($saldoMonth[$i] ?? 0));
        }

        return $rows;
    }

    /** @param  array<int, int>  $addMonth
     * @return array<string, int>
     */
    private function buildAddMonthRows(array $addMonth): array
    {
        $rows = [];
        for ($i = 0; $i < 12; $i++) {
            $rows['v_bdgt_addmonth'.($i + 1)] = max(0, (int) ($addMonth[$i] ?? 0));
        }

        return $rows;
    }
}
