<?php

namespace App\Repositories;

use App\Models\Tmempsalpay;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class TmemSalPayRepository
{
    /**
     * @return Collection<int, Tmempsalpay>
     */
    public function findRekapCostCenter(
        string $tanggalProsesGaji,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
        ?array $orgEselon = null,
    ): Collection {
        return Tmempsalpay::query()
            ->from('tmempsalpay as a')
            ->join('trorg as b', 'a.c_org_cur', '=', 'b.c_org_cur')
            ->selectRaw(
                'SUBSTRING(a.c_org_cur, 1, 2) AS c_org_echl'
            )
            ->selectRaw(
                'a.c_org_cur AS kode_unit_organisasi'
            )
            ->selectRaw(
                'b.n_org AS nama_unit_organisasi'
            )
            ->selectRaw(
                'a.c_emp_payloc AS lokasi'
            )
            ->selectRaw(
                'SUM(a.v_emp_tunjgaji) AS besar_gaji'
            )
            ->selectRaw(
                'SUM(a.v_emp_potgaji) AS pihak_lain'
            )
            ->selectRaw(
                'SUM(a.v_emp_tunjgaji) - SUM(a.v_emp_potgaji) AS yang_bersangkutan'
            )
            ->where('a.d_proc_gaji', $tanggalProsesGaji)
            ->where(
                'a.c_bank_gaji',
                'like',
                $bankKas !== null && $bankKas !== '' ? $bankKas : '%'
            )
            ->where(
                'a.c_emp_payloc',
                'like',
                $lokasi !== null && $lokasi !== '' ? $lokasi : '%'
            )
            ->where(
                'a.i_inv_gaji',
                'like',
                $nomorBukti !== null && $nomorBukti !== '' ? $nomorBukti : '%'
            )
            ->when(
                ! empty($orgEselon),
                fn ($query) => $query->whereIn(
                    DB::raw('SUBSTRING(a.c_org_cur, 1, 2)'),
                    $orgEselon
                )
            )
            ->groupBy(
                'a.c_org_cur',
                'b.n_org',
                'a.c_emp_payloc'
            )
            ->orderByRaw('1, 2, 4')
            ->get();
    }
}
