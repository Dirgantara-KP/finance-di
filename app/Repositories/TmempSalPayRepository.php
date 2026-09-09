<?php

namespace App\Repositories;

use App\Models\Tmempsalpay;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TmempSalPayRepository
{
    public function findRekapCostCenter(
        string $tanggalProsesGaji,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
    ): Collection {
        return Tmempsalpay::query()
            ->from('tmempsalpay as a')
            ->join(
                'trorg as b',
                'a.c_org_cur',
                '=',
                'b.c_org_cur'
            )
            ->selectRaw(
                'SUBSTRING(a.c_org_cur, 1, 2) AS org_echl'
            )
            ->selectRaw(
                'a.c_org_cur AS org_cur'
            )
            ->selectRaw(
                'a.c_org_cur AS kode_unit_organisasi'
            )
            ->selectRaw(
                'b.n_org AS nama_unit_organisasi'
            )
            ->selectRaw(
                "CONCAT(a.c_org_cur, ' - ', b.n_org) AS cost_center"
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
            ->where(
                'a.d_proc_gaji',
                $tanggalProsesGaji
            )
            ->where(
                'a.c_bank_gaji',
                'like',
                filled($bankKas) ? $bankKas : '%'
            )
            ->where(
                'a.c_emp_payloc',
                'like',
                filled($lokasi) ? $lokasi : '%'
            )
            ->where(
                'a.i_inv_gaji',
                'like',
                filled($nomorBukti) ? $nomorBukti : '%'
            )
            ->groupBy(
                'a.c_org_cur',
                'b.n_org',
                'a.c_emp_payloc'
            )
            ->orderByRaw('1, 2, 4')
            ->get();
    }

    public function existsForFilter(
        string $tanggalProsesGaji,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
    ): bool {
        return Tmempsalpay::query()
            ->from('tmempsalpay as a')
            ->where(
                'a.d_proc_gaji',
                $tanggalProsesGaji
            )
            ->where(
                'a.c_bank_gaji',
                'like',
                filled($bankKas) ? $bankKas : '%'
            )
            ->where(
                'a.c_emp_payloc',
                'like',
                filled($lokasi) ? $lokasi : '%'
            )
            ->where(
                'a.i_inv_gaji',
                'like',
                filled($nomorBukti) ? $nomorBukti : '%'
            )
            ->exists();
    }

    /**
     * INSERT sesuai FD.
     *
     * Organisasi/eselon tidak lagi diminta dari UI.
     * Semua c_org_echl yang memang cocok dengan filter
     * akan ikut masuk.
     */
    public function insertFromVempSalPay(
        string $periodeGaji,
        string $nomorBuktiGaji,
        string $bankGaji,
        string $lokasiBayar,
        string $iUser,
    ): int {
        $sql = <<<'SQL'
            INSERT INTO tmempsalpay (
                d_proc_gaji,
                c_bank_gaji,
                c_emp_payloc,
                c_org_cur,
                c_cost,
                i_inv_gaji,
                c_cy,
                v_emp_tunjgaji,
                v_emp_potgaji,
                v_emp_gaji,
                c_org_id,
                c_sal_paystat,
                c_org_payrecpt,
                i_inv_payrecpt,
                d_inv_payrecpt,
                i_entry,
                d_entry,
                c_pot_paystat,
                c_org_payrecpt1,
                i_inv_payrecpt1
            )
            SELECT
                d_proc_gaji,
                c_bank_gaji,
                c_emp_payloc,
                c_org_cur,
                c_cost,
                i_jour,
                'IDR',
                SUM(v_emp_tunjgaji),
                SUM(v_emp_potgaji),
                SUM(v_emp_tunjgaji) - SUM(v_emp_potgaji),
                'CO',
                '-',
                '-',
                '-',
                NULL,
                ?,
                NOW(),
                '-',
                '-',
                '-'
            FROM vempsalpay
            WHERE d_proc_gaji = ?
              AND i_jour LIKE ?
              AND c_bank_gaji LIKE ?
              AND c_emp_payloc LIKE ?
            GROUP BY
                d_proc_gaji,
                c_bank_gaji,
                c_emp_payloc,
                c_org_cur,
                c_cost,
                i_jour
            SQL;

        return DB::affectingStatement($sql, [
            $iUser,
            $periodeGaji,
            $nomorBuktiGaji,
            $bankGaji,
            $lokasiBayar,
        ]);
    }

    public function deleteByFilter(
        string $periodeGaji,
        string $nomorBuktiGaji,
        string $bankGaji,
        string $lokasiBayar,
    ): int {
        return DB::delete(
            'DELETE FROM tmempsalpay
             WHERE d_proc_gaji = ?
               AND i_inv_gaji LIKE ?
               AND c_org_id = ?
               AND c_bank_gaji LIKE ?
               AND c_emp_payloc LIKE ?',
            [
                $periodeGaji,
                $nomorBuktiGaji,
                'CO',
                $bankGaji,
                $lokasiBayar,
            ]
        );
    }
}