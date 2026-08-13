<?php

namespace Database\Seeders;

use App\Models\Vororg;
use Illuminate\Database\Seeder;

class VororgSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/vrorg.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = [
                'i_org' => trim($data[0]),
                'i_org_ut' => $data[1],
                'i_org_dir' => $data[2],
                'i_org_subdir' => trim($data[3]),
                'i_org_div' => trim($data[4]),
                'i_org_subdiv' => trim($data[5]),
                'i_org_dept' => trim($data[6]),
                'i_org_subdept' => trim($data[7]),
                'i_org_bid' => trim($data[8]),
                'i_org_subbid' => trim($data[9]),
                'i_org_00' => trim($data[10]),
                'c_org_statlvl' => $data[11],
                'c_org_cur' => trim($data[12]),
                'c_org_parent' => trim($data[13]),
                'c_org_div' => trim($data[14]),
                'c_org_subdir' => trim($data[15]),
                'c_org_direktorat' => trim($data[16]),
                'n_org_cur' => trim($data[17]),
                'n_org_cur_short' => trim($data[18]),
                'n_org_english' => trim($data[19]),
                'n_org_shortenglish' => trim($data[20]),
                'n_org_direktorat' => trim($data[21]),
                'n_org_direktorat_short' => trim($data[22]),
                'i_emp_mngr' => $data[23],
                'n_emp' => trim($data[24]),
                'c_org_assetstat' => trim($data[25]),
                'd_org_start' => trim($data[26]) ?: null,
                'd_org_finish' => trim($data[27]) ?: null,
                'c_pos_grpf' => trim($data[28]),
                'n_pos_title' => trim($data[29]),
                'c_pos_grp' => trim($data[30]),
                'n_pos_titlestrukt' => trim($data[31]),
            ];

            if (count($rows) >= 500) {
                Vororg::query()->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            Vororg::query()->insert($rows);
        }

        fclose($handle);

        $this->command->info('VororgSeeder selesai: '.Vororg::query()->count().' baris.');
    }
}
