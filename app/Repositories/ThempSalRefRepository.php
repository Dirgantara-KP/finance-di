<?php

namespace App\Repositories;

class ThempSalRefRepository
{
    /**
     * UPDATE THEMPSALREF
     * SET c_org_payrecpt = :OrgCur
     * WHERE d_proc_gaji = :periode_gaji
     * AND SUBSTR(c_org_loan, 1, 2) IN (:OrgGaji);
     *
     * Query FD item H — TARGET tabel ini memang THEMPSALREF, bukan
     * TMEMPSALPAY. Jangan diarahkan ke TMEMPSALPAY meskipun INSERT
     * menyasar tabel itu.
     *
     * @param  array<int, string>  $orgGaji  daftar kode eselon 2 digit
     * @return int jumlah baris yang ter-update
     */
    public function updateOrgPayrecpt(
        string $periodeGaji,
        string $orgCur,
        array $orgGaji,
    ): int {
        if (empty($orgGaji)) {
            throw new \InvalidArgumentException('Daftar organisasi/eselon (:OrgGaji) wajib diisi.');
        }

        $placeholders = implode(',', array_fill(0, count($orgGaji), '?'));

        return \DB::affectingStatement(
            "UPDATE thempsalref
             SET c_org_payrecpt = ?
             WHERE d_proc_gaji = ?
               AND SUBSTR(c_org_loan, 1, 2) IN ({$placeholders})",
            [$orgCur, $periodeGaji, ...$orgGaji]
        );
    }
}