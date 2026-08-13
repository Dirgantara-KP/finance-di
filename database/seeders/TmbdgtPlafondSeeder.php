<?php

namespace Database\Seeders;

use App\Models\TmbdgtPlafond;
use Illuminate\Database\Seeder;

class TmbdgtPlafondSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/tmbdgtplafond.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, 0, ';');
        $rows = [];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $num = fn (int $i): string|int => $data[$i] === '' ? 0 : $data[$i];

            $rows[] = [
                'c_source' => trim($data[0]),
                'c_org_id' => trim($data[1]),
                'c_org' => trim($data[2]),
                'i_contr' => trim($data[3]),
                'c_bdgt_contrstat' => trim($data[4]),
                'c_bdgt_contrinex' => trim($data[5]),
                'c_bdgt_anggaran' => trim($data[6]),
                'c_pgm' => trim($data[7]),
                'c_pgm_sub' => trim($data[8]),
                'c_pgm_ver' => trim($data[9]),
                'c_coa_dr' => trim($data[10]),
                'c_coa_cr' => trim($data[11]),
                'c_cy' => trim($data[12]),
                'v_bdgt_saldomonth1' => $num(26),
                'v_bdgt_saldomonth2' => $num(27),
                'v_bdgt_saldomonth3' => $num(28),
                'v_bdgt_saldomonth4' => $num(29),
                'v_bdgt_saldomonth5' => $num(30),
                'v_bdgt_saldomonth6' => $num(31),
                'v_bdgt_saldomonth7' => $num(32),
                'v_bdgt_saldomonth8' => $num(33),
                'v_bdgt_saldomonth9' => $num(34),
                'v_bdgt_saldomonth10' => $num(35),
                'v_bdgt_saldomonth11' => $num(36),
                'v_bdgt_saldomonth12' => $num(37),
                'v_bdgt_saldototal' => $num(38),
                'i_entry' => trim($data[39]),
                'd_entry' => trim($data[40]),
                'c_org_center' => trim($data[41]),
                'v_bdgt_plantotal' => $num(56),
                'c_org_contr' => trim($data[60]),
                'v_bdgt_addmonth1' => $num(61),
                'v_bdgt_addmonth2' => $num(62),
                'v_bdgt_addmonth3' => $num(63),
                'v_bdgt_addmonth4' => $num(64),
                'v_bdgt_addmonth5' => $num(65),
                'v_bdgt_addmonth6' => $num(66),
                'v_bdgt_addmonth7' => $num(67),
                'v_bdgt_addmonth8' => $num(68),
                'v_bdgt_addmonth9' => $num(69),
                'v_bdgt_addmonth10' => $num(70),
                'v_bdgt_addmonth11' => $num(71),
                'v_bdgt_addmonth12' => $num(72),
                'v_bdgt_addtotal' => $num(73),
            ];

            if (count($rows) >= 500) {
                TmbdgtPlafond::query()->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            TmbdgtPlafond::query()->insert($rows);
        }

        fclose($handle);

        $this->command->info('TmbdgtPlafondSeeder selesai: '.TmbdgtPlafond::query()->count().' baris.');
    }
}
