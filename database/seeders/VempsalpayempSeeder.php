<?php

namespace Database\Seeders;

use App\Models\Vempsalpayemp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class VempsalpayempSeeder extends Seeder
{
    /**
     * Import data uji dari vempsalpayemp.csv yang diberikan.
     * Header CSV: I_EMP, D_PROC_GAJI, I_JOUR, C_ORG_CUR, C_ORG_ASAL, C_BANK_GAJI, C_EMP_PAYLOC, V_EMP_TUNJGAJI, V_EMP_POTGAJI
     */
    public function run(): void
    {
        $path = database_path('seeders/data/vempsalpayemp.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = [
                'i_emp' => $data[0],
                'd_proc_gaji' => Carbon::parse($data[1])->toDateString(),
                'i_jour' => $data[2],
                'c_org_cur' => $data[3],
                'c_org_asal' => $data[4],
                'c_bank_gaji' => $data[5],
                'c_emp_payloc' => $data[6],
                'v_emp_tunjgaji' => $data[7],
                'v_emp_potgaji' => $data[8],
            ];

            if (count($rows) >= 500) {
                Vempsalpayemp::query()->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            Vempsalpayemp::query()->insert($rows);
        }

        fclose($handle);

        $this->command->info('VempsalpayempSeeder selesai: '.Vempsalpayemp::query()->count().' baris.');
    }
}