<?php

namespace App\Repositories;

use App\Models\VempSalPay;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VempSalPayRepository
{
    /**
     * @param  array<int, string>|null  $orgEselon
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

    /**
     * Rekap Cost Center dari data MENTAH VEMPSALPAY (dipakai saat kombinasi
     * filter belum punya baris di TMEMPSALPAY). Query FD point E, join TRORG,
     * GROUP BY termasuk c_cost — jangan dihilangkan karena dipakai lagi saat
     * INSERT ke TMEMPSALPAY.
     *
     * @param  array<int, string>|null  $orgEselon
     */
    public function findRekapCostCenter(
        string $periodeGaji,
        ?string $nomorGaji = null,
        ?array $orgEselon = null,
        ?string $bankGaji = null,
        ?string $lokasiBayar = null,
    ): Collection {
        return Vempsalpay::query()
            ->from('vempsalpay as a')
            ->join('trorg as b', 'a.c_org_cur', '=', 'b.c_org_cur')
            ->selectRaw('a.c_org_echl AS c_org_echl')
            ->selectRaw('a.c_org_cur AS kode_unit_organisasi')
            ->selectRaw('b.n_org AS nama_unit_organisasi')
            ->selectRaw('a.c_emp_payloc AS lokasi')
            ->selectRaw('SUM(a.v_emp_tunjgaji) AS v_tot_tunjgaji')
            ->selectRaw('SUM(a.v_emp_potgaji) AS v_tot_potgaji')
            ->selectRaw('SUM(a.v_emp_tunjgaji) - SUM(a.v_emp_potgaji) AS v_gaji_bersih')
            ->selectRaw('a.c_cost AS coa')
            ->where('a.d_proc_gaji', $periodeGaji)
            ->where(
                'a.i_jour',
                'like',
                $nomorGaji !== null && $nomorGaji !== '' ? $nomorGaji : '%'
            )
            ->when(
                ! empty($orgEselon),
                fn ($q) => $q->whereIn('a.c_org_echl', $orgEselon)
            )
            ->where(
                'a.c_bank_gaji',
                'like',
                $bankGaji !== null && $bankGaji !== '' ? $bankGaji : '%'
            )
            ->where(
                'a.c_emp_payloc',
                'like',
                $lokasiBayar !== null && $lokasiBayar !== '' ? $lokasiBayar : '%'
            )
            ->groupBy('a.c_org_echl', 'a.c_org_cur', 'b.n_org', 'a.c_emp_payloc', 'a.c_cost')
            ->orderByRaw('1, 2, 4')
            ->get();
    }
}