<?php

namespace App\Repositories;

use App\Models\VempSalPay;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class VempSalPayRepository
{
    /**
     * @param  array<int, string>|null  $orgEselon
     * @return Collection<int, VempSalPay>
     */
    public function findByPeriode(string $periode, ?array $orgEselon = null): Collection
    {
        return VempSalPay::query()
            ->selectRaw(
                'DISTINCT i_jour AS NOMOR_BUKTI_GAJI, c_bank_gaji AS DIBAYAR_VIA, c_emp_payloc AS LOKASI'
            )
            ->whereRaw("DATE_FORMAT(d_proc_gaji, '%Y-%m') = ?", [$periode])
            ->when(
                ! empty($orgEselon),
                fn ($q) => $q->whereIn(DB::raw('SUBSTRING(c_org_cur, 1, 2)'), $orgEselon)
            )
            ->groupBy('i_jour', 'c_bank_gaji', 'c_emp_payloc')
            ->orderBy('NOMOR_BUKTI_GAJI')
            ->orderBy('DIBAYAR_VIA')
            ->orderBy('LOKASI')
            ->get();
    }
}
