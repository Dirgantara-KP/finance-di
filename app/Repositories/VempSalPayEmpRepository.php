<?php

namespace App\Repositories;

use App\Models\VempSalPayEmp;
use Illuminate\Support\Collection;

class VempSalPayEmpRepository
{
    public function findByCostCenter(
        string $tanggalProsesGaji,
        string $orgCur,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
    ): Collection {
        $bank = filled($bankKas)
            ? $bankKas
            : '%';

        $lok = filled($lokasi)
            ? $lokasi
            : '%';

        $jour = filled($nomorBukti)
            ? $nomorBukti
            : '%';

        $withNama = VempSalPayEmp::query()
            ->from('vempsalpayemp as a')
            ->join(
                'tprrmempii as b',
                'a.i_emp',
                '=',
                'b.i_emp'
            )
            ->selectRaw('a.i_emp AS nik')
            ->selectRaw('b.n_emp AS nama')
            ->selectRaw('a.c_org_asal AS unit_org')
            ->selectRaw('a.c_bank_gaji AS via')
            ->selectRaw('a.c_emp_payloc AS lokasi')
            ->selectRaw(
                'SUM(a.v_emp_tunjgaji) AS besar_gaji'
            )
            ->selectRaw(
                'SUM(a.v_emp_potgaji) AS pihak_lain'
            )
            ->selectRaw(
                'SUM(a.v_emp_tunjgaji) - SUM(a.v_emp_potgaji) AS yang_bersangkutan'
            )
            ->where(
                'a.d_proc_gaji',
                $tanggalProsesGaji
            )
            ->where(
                'a.i_jour',
                'like',
                $jour
            )
            ->where(
                'a.c_org_cur',
                $orgCur
            )
            ->where(
                'a.c_bank_gaji',
                'like',
                $bank
            )
            ->where(
                'a.c_emp_payloc',
                'like',
                $lok
            )
            ->groupBy(
                'a.i_emp',
                'b.n_emp',
                'a.c_org_asal',
                'a.c_bank_gaji',
                'a.c_emp_payloc'
            );

        $withoutNama = VempSalPayEmp::query()
            ->from('vempsalpayemp as a')
            ->selectRaw('a.i_emp AS nik')
            ->selectRaw("'-' AS nama")
            ->selectRaw('a.c_org_asal AS unit_org')
            ->selectRaw('a.c_bank_gaji AS via')
            ->selectRaw('a.c_emp_payloc AS lokasi')
            ->selectRaw(
                'SUM(a.v_emp_tunjgaji) AS besar_gaji'
            )
            ->selectRaw(
                'SUM(a.v_emp_potgaji) AS pihak_lain'
            )
            ->selectRaw(
                'SUM(a.v_emp_tunjgaji) - SUM(a.v_emp_potgaji) AS yang_bersangkutan'
            )
            ->where(
                'a.d_proc_gaji',
                $tanggalProsesGaji
            )
            ->where(
                'a.i_jour',
                'like',
                $jour
            )
            ->where(
                'a.c_org_cur',
                $orgCur
            )
            ->where(
                'a.c_bank_gaji',
                'like',
                $bank
            )
            ->where(
                'a.c_emp_payloc',
                'like',
                $lok
            )
            ->whereNotExists(
                function ($query) {
                    $query->selectRaw('1')
                        ->from('tprrmempii as b')
                        ->whereColumn(
                            'b.i_emp',
                            'a.i_emp'
                        );
                }
            )
            ->groupBy(
                'a.i_emp',
                'a.c_org_asal',
                'a.c_bank_gaji',
                'a.c_emp_payloc'
            );

        return $withNama
            ->unionAll($withoutNama)
            ->orderBy('nik')
            ->get();
    }
}