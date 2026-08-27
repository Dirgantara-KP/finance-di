<?php

namespace App\Repositories;

use App\Models\Tmempsalpay;
use Illuminate\Support\Collection;

class TmempSalPayRepository
{
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
            ->selectRaw('SUBSTRING(a.c_org_cur, 1, 2) AS c_org_echl')
            ->selectRaw('a.c_org_cur AS kode_unit_organisasi')
            ->selectRaw('b.n_org AS nama_unit_organisasi')
            ->selectRaw('a.c_emp_payloc AS lokasi')
            ->selectRaw('SUM(a.v_emp_tunjgaji) AS besar_gaji')
            ->selectRaw('SUM(a.v_emp_potgaji) AS pihak_lain')
            ->selectRaw('SUM(a.v_emp_tunjgaji) - SUM(a.v_emp_potgaji) AS yang_bersangkutan')
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
                    \DB::raw('SUBSTRING(a.c_org_cur, 1, 2)'),
                    $orgEselon
                )
            )
            ->groupBy('a.c_org_cur', 'b.n_org', 'a.c_emp_payloc')
            ->orderByRaw('1, 2, 4')
            ->get();
    }

    /**
     * Cek apakah kombinasi filter (tanggal, no bukti, bank, lokasi) SUDAH
     * pernah di-collecting (ada barisnya di TMEMPSALPAY). Dipakai SHOW untuk
     * menentukan sumber data (TMEMPSALPAY vs VEMPSALPAY) dan untuk
     * enable/disable tombol Insert/Update.
     */
    public function existsForFilter(
        string $tanggalProsesGaji,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
    ): bool {
        return Tmempsalpay::query()
            ->from('tmempsalpay as a')
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
            ->exists();
    }

    /**
     * INSERT INTO TMEMPSALPAY ... SELECT ... FROM VEMPSALPAY, persis query
     * FD item F. Data sumber diagregasi per (d_proc_gaji, c_bank_gaji,
     * c_emp_payloc, c_org_cur, c_cost, i_jour) langsung dari VEMPSALPAY —
     * bukan dari Rekap Cost Center yang sudah ditampilkan di UI — supaya
     * hasil insert selalu konsisten dengan data mentah terbaru.
     *
     * @param  array<int, string>|null  $orgEselon
     */
    public function insertFromVempSalPay(
        string $periodeGaji,
        string $nomorBuktiGaji,
        ?array $orgEselon,
        string $bankGaji,
        string $lokasiBayar,
        string $orgId,
        string $iUser,
    ): int {
        // Filter organisasi/eselon bersifat opsional (whitelist kode 2 digit).
        $orgEselonSql = '1=1';
        $orgEselonBindings = [];
        if (! empty($orgEselon)) {
            $placeholders = implode(',', array_fill(0, count($orgEselon), '?'));
            $orgEselonSql = "c_org_echl IN ({$placeholders})";
            $orgEselonBindings = $orgEselon;
        }

        // Urutan kolom INSERT harus PERSIS sama dengan urutan value SELECT.
        // 11 kolom terakhir (c_org_id s/d i_inv_payrecpt1) diisi sesuai
        // literal/placeholder FD item F: '-' untuk status default,
        // NULL untuk d_inv_payrecpt, NOW() untuk d_entry (setara SYSDATE).
        $sql = <<<'SQL'
            INSERT INTO tmempsalpay (
                d_proc_gaji, c_bank_gaji, c_emp_payloc, c_org_cur, c_cost, i_inv_gaji, c_cy,
                v_emp_tunjgaji, v_emp_potgaji, v_emp_gaji,
                c_org_id, c_sal_paystat, c_org_payrecpt, i_inv_payrecpt, d_inv_payrecpt,
                i_entry, d_entry, c_pot_paystat, c_org_payrecpt1, i_inv_payrecpt1
            )
            SELECT
                d_proc_gaji, c_bank_gaji, c_emp_payloc, c_org_cur, c_cost, i_jour, 'IDR',
                SUM(v_emp_tunjgaji), SUM(v_emp_potgaji), SUM(v_emp_tunjgaji) - SUM(v_emp_potgaji),
                ?, '-', '-', '-', NULL,
                ?, NOW(), '-', '-', '-'
            FROM vempsalpay
            WHERE d_proc_gaji = ?
              AND i_jour LIKE ?
              AND {orgEselonSql}
              AND c_bank_gaji LIKE ?
              AND c_emp_payloc LIKE ?
            GROUP BY d_proc_gaji, c_bank_gaji, c_emp_payloc, c_org_cur, c_cost, i_jour
            SQL;

        $sql = str_replace('{orgEselonSql}', $orgEselonSql, $sql);

        return \DB::affectingStatement($sql, [
            $orgId,                 // -> c_org_id
            $iUser,                 // -> i_entry
            $periodeGaji,           // WHERE d_proc_gaji
            $nomorBuktiGaji,        // WHERE i_jour LIKE
            ...$orgEselonBindings,  // WHERE c_org_echl IN (...)
            $bankGaji,              // WHERE c_bank_gaji LIKE
            $lokasiBayar,           // WHERE c_emp_payloc LIKE
        ]);
    }
}