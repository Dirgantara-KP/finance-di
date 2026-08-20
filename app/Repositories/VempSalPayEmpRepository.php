<?php

namespace App\Repositories;

use App\Models\VempSalPayEmp;
use Illuminate\Database\Eloquent\Collection;

final class VempSalPayEmpRepository
{
    /**
     * @return Collection<int, VempSalPayEmp>
     */
    public function findRekapCostCenter(
        string $tanggalProsesGaji,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
    ): Collection {
        return VempSalPayEmp::query()
            ->from('vempsalpayemp as a')
            ->join('trorg as b', 'a.c_org_cur', '=', 'b.c_org_cur')
            ->selectRaw('a.c_org_cur AS kode_unit_organisasi')
            ->selectRaw('b.n_org AS nama_unit_organisasi')
            ->selectRaw('a.c_emp_payloc AS lokasi')
            ->selectRaw('SUM(a.v_emp_tunjgaji) AS besar_gaji')
            ->selectRaw('SUM(a.v_emp_potgaji) AS pihak_lain')
            ->selectRaw('SUM(a.v_emp_tunjgaji) - SUM(a.v_emp_potgaji) AS yang_bersangkutan')
            ->where('a.d_proc_gaji', $tanggalProsesGaji)
            ->where('a.c_bank_gaji', 'like', $bankKas !== null && $bankKas !== '' ? $bankKas : '%')
            ->where('a.c_emp_payloc', 'like', $lokasi !== null && $lokasi !== '' ? $lokasi : '%')
            ->where('a.i_jour', 'like', $nomorBukti !== null && $nomorBukti !== '' ? $nomorBukti : '%')
            ->groupBy('a.c_org_cur', 'b.n_org', 'a.c_emp_payloc')
            ->orderByRaw('1, 2, 3')
            ->get();
    }

    /**
     * @return Collection<int, VempSalPayEmp>
     */
    public function findByCostCenter(
        string $tanggalProsesGaji,
        string $orgCur,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
    ): Collection {
        return VempSalPayEmp::query()
            ->from('vempsalpayemp as a')
            ->leftJoin('tprrmempii as b', 'a.i_emp', '=', 'b.i_emp')
            ->selectRaw('a.i_emp AS nik')
            ->selectRaw('COALESCE(b.n_emp, ?) AS nama', ['-'])
            ->selectRaw('a.c_org_asal AS unit_org')
            ->selectRaw('a.c_bank_gaji AS via')
            ->selectRaw('a.c_emp_payloc AS lokasi')
            ->selectRaw('SUM(a.v_emp_tunjgaji) AS besar_gaji')
            ->selectRaw('SUM(a.v_emp_potgaji) AS pihak_lain')
            ->selectRaw('SUM(a.v_emp_tunjgaji) - SUM(a.v_emp_potgaji) AS yang_bersangkutan')
            ->where('a.d_proc_gaji', $tanggalProsesGaji)
            ->where('a.c_org_cur', $orgCur)
            ->where('a.c_bank_gaji', 'like', $bankKas !== null && $bankKas !== '' ? $bankKas : '%')
            ->where('a.c_emp_payloc', 'like', $lokasi !== null && $lokasi !== '' ? $lokasi : '%')
            ->where('a.i_jour', 'like', $nomorBukti !== null && $nomorBukti !== '' ? $nomorBukti : '%')
            ->groupBy('a.i_emp', 'b.n_emp', 'a.c_org_asal', 'a.c_bank_gaji', 'a.c_emp_payloc')
            ->orderByRaw('(b.i_emp IS NULL), a.i_emp')
            ->get();
    }
}
