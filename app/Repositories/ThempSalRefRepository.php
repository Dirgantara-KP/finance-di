<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ThempSalRefRepository
{
    public function updateOrgPayrecpt(
        string $periodeGaji,
        string $orgCur,
        array $orgGaji,
    ): int {
        if (empty($orgGaji)) {
            return 0;
        }

        $placeholders = implode(
            ',',
            array_fill(
                0,
                count($orgGaji),
                '?'
            )
        );

        return DB::update(
            "UPDATE thempsalref
             SET c_org_payrecpt = ?
             WHERE d_proc_gaji = ?
               AND SUBSTR(c_org_loan, 1, 2)
                   IN ({$placeholders})",
            [
                $orgCur,
                $periodeGaji,
                ...$orgGaji,
            ]
        );
    }
}