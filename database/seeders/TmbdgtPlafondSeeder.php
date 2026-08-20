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
            $months = fn (int $first, string $key): array => array_combine(
                array_map(fn (int $i) => $key.$i, range(1, 12)),
                array_map($num, range($first, $first + 11)),
            );

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
                ...$months(13, 'v_bdgt_month'),
                'v_bdgt_total' => $num(25),
                ...$months(26, 'v_bdgt_saldomonth'),
                'v_bdgt_saldototal' => $num(38),
                'i_entry' => trim($data[39]),
                'd_entry' => trim($data[40]),
                'c_org_center' => trim($data[41]),
                'c_org_dit' => trim($data[42]),
                'c_bdgt_cat' => trim($data[43]),
                ...$months(44, 'v_bdgt_planmonth'),
                'v_bdgt_plantotal' => $num(56),
                'c_stat_bdgt' => trim($data[57]),
                'i_ref_updbdgt' => trim($data[58]),
                'd_ref_updbdgt' => trim($data[59]),
                'c_org_contr' => trim($data[60]),
                ...$months(61, 'v_bdgt_addmonth'),
                'v_bdgt_addtotal' => $num(73),
                'c_bdgt_stat' => trim($data[74]),
                'i_pmn' => trim($data[75]),
                'd_pmn' => trim($data[76]),
                'i_order' => trim($data[77]),
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
