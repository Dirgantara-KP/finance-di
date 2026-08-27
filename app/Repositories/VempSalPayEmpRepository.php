<?php

namespace App\Repositories;

use App\Models\VempSalPayEmp;
use Illuminate\Support\Collection;

class VempSalPayEmpRepository
{
    /**
     * Rincian gaji per karyawan untuk satu Cost Center (c_org_cur) pada
     * periode proses gaji tertentu. LEFT JOIN ke TPRRMEMPII (master
     * pegawai) — nama karyawan tampil "-" apabila NIK tidak ditemukan.
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
            ->selectRaw('b.n_emp AS nama')
            ->selectRaw('a.c_org_asal AS unit_org')
            ->selectRaw('a.c_bank_gaji AS via')
            ->selectRaw('a.c_emp_payloc AS lokasi')
            ->selectRaw('a.v_emp_tunjgaji AS besar_gaji')
            ->selectRaw('a.v_emp_potgaji AS pihak_lain')
            ->selectRaw('a.v_emp_tunjgaji - a.v_emp_potgaji AS yang_bersangkutan')
            ->where('a.d_proc_gaji', $tanggalProsesGaji)
            ->where('a.c_org_cur', $orgCur)
            ->where('a.c_bank_gaji', 'like', $bankKas !== null && $bankKas !== '' ? $bankKas : '%')
            ->where('a.c_emp_payloc', 'like', $lokasi !== null && $lokasi !== '' ? $lokasi : '%')
            ->where('a.i_jour', 'like', $nomorBukti !== null && $nomorBukti !== '' ? $nomorBukti : '%')
            ->orderBy('a.i_emp')
            ->get();
    }
}