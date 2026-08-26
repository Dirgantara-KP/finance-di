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

        Vororg::query()->truncate();

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $str = fn (int $i): ?string => isset($data[$i]) && trim($data[$i]) !== '' ? trim($data[$i]) : null;

            $rows[] = [
                'i_org' => trim($data[0]),
                'i_org_ut' => (int) $data[1],
                'i_org_dir' => (int) $data[2],
                'i_org_subdir' => trim($data[3]),
                'i_org_div' => trim($data[4]),
                'i_org_subdiv' => trim($data[5]),
                'i_org_dept' => trim($data[6]),
                'i_org_subdept' => trim($data[7]),
                'i_org_bid' => trim($data[8]),
                'i_org_subbid' => trim($data[9]),
                'i_org_00' => trim($data[10]),
                'c_org_statlvl' => (int) $data[11],
                'c_org_cur' => trim($data[12]),
                'c_org_parent' => $str(13),
                'c_org_div' => $str(14),
                'c_org_subdir' => $str(15),
                'c_org_direktorat' => trim($data[16]),
                'n_org_cur' => trim($data[17]),
                'n_org_cur_short' => $str(18),
                'n_org_english' => $str(19),
                'n_org_shortenglish' => $str(20),
                'n_org_direktorat' => $str(21),
                'n_org_direktorat_short' => $str(22),
                'i_emp_mngr' => $str(23) !== null ? (int) $str(23) : null,
                'n_emp' => $str(24),
                'c_org_assetstat' => $str(25) ?? 'OPN',
                'd_org_start' => $str(26),
                'd_org_finish' => $str(27),
                'c_pos_grpf' => $str(28),
                'n_pos_title' => $str(29),
                'c_pos_grp' => $str(30),
                'n_pos_titlestrukt' => $str(31),
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
