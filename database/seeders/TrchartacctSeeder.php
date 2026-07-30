<?php

namespace Database\Seeders;

use App\Models\Trchartacct;
use Illuminate\Database\Seeder;

class TrchartacctSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['c_cost_bsis' => 'CC', 'c_cost_acctgrp' => 2, 'c_cost_acctsub' => '1', 'c_cost_acctsubgrp' => 6, 'c_cost' => '65Z', 'e_cost' => 'BIAYA PREMI ASSURANSI LAINNYA'],
            ['c_cost_bsis' => 'CC', 'c_cost_acctgrp' => 2, 'c_cost_acctsub' => '1', 'c_cost_acctsubgrp' => 1, 'c_cost' => '66D', 'e_cost' => 'BIAYA PEMAKAIAN GAS UNTUK PRODUKSI'],
            ['c_cost_bsis' => 'CC', 'c_cost_acctgrp' => 2, 'c_cost_acctsub' => '1', 'c_cost_acctsubgrp' => 7, 'c_cost' => '66G', 'e_cost' => 'BIAYA UTILITAS DAN KOMUNIKASI LAINNYA'],
            ['c_cost_bsis' => 'CC', 'c_cost_acctgrp' => 2, 'c_cost_acctsub' => '1', 'c_cost_acctsubgrp' => 8, 'c_cost' => '67A', 'e_cost' => 'BIAYA ALAT TULIS-KANTOR'],
            ['c_cost_bsis' => 'CC', 'c_cost_acctgrp' => 2, 'c_cost_acctsub' => '1', 'c_cost_acctsubgrp' => 9, 'c_cost' => '68D', 'e_cost' => 'BIAYA PENGELOLAN MATERIAL LAINNYA'],
            ['c_cost_bsis' => 'CC', 'c_cost_acctgrp' => 2, 'c_cost_acctsub' => '1', 'c_cost_acctsubgrp' => 10, 'c_cost' => '69B', 'e_cost' => 'BIAYA BUNGA MODAL KERJA'],
            ['c_cost_bsis' => 'CC', 'c_cost_acctgrp' => 2, 'c_cost_acctsub' => '1', 'c_cost_acctsubgrp' => 11, 'c_cost' => '70A', 'e_cost' => 'BIAYA ENTERTAINMENT'],
            ['c_cost_bsis' => 'CC', 'c_cost_acctgrp' => 2, 'c_cost_acctsub' => '1', 'c_cost_acctsubgrp' => 11, 'c_cost' => '70F', 'e_cost' => 'BIAYA TAMU PERUSAHAAN'],
            ['c_cost_bsis' => 'CC', 'c_cost_acctgrp' => 2, 'c_cost_acctsub' => '1', 'c_cost_acctsubgrp' => 11, 'c_cost' => '70Z', 'e_cost' => 'BIAYA LAIN-LAIN'],
            ['c_cost_bsis' => 'CC', 'c_cost_acctgrp' => 2, 'c_cost_acctsub' => '1', 'c_cost_acctsubgrp' => 11, 'c_cost' => '70L', 'e_cost' => 'JASA PELELANGAN'],
        ];

        foreach ($data as $row) {
            Trchartacct::create($row);
        }
    }
}
