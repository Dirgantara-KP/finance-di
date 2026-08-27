<?php

namespace Database\Seeders;

use App\Models\Tmempsalpay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TmempsalpaySeeder extends Seeder
{
    /**
     * Import data uji dari tmempsalpay.csv (500 baris) yang diberikan.
     * Header CSV: D_PROC_GAJI, C_ORG_CUR, I_INV_GAJI, C_BANK_GAJI, C_EMP_PAYLOC, V_EMP_TUNJGAJI, V_EMP_POTGAJI
     */
    public function run(): void
    {
        $path = database_path('seeders/data/tmempsalpay.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = [
                'd_proc_gaji' => Carbon::parse($data[0])->toDateString(),
                'c_org_cur' => $data[1],
                'i_inv_gaji' => $data[2],
                'c_bank_gaji' => $data[3],
                'c_emp_payloc' => $data[4],
                'v_emp_tunjgaji' => $data[5],
                'v_emp_potgaji' => $data[6],
            ];

            // Insert per 500 baris supaya tidak terlalu berat sekali jalan.
            if (count($rows) >= 500) {
                Tmempsalpay::query()->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            Tmempsalpay::query()->insert($rows);
        }

        fclose($handle);

        $this->command->info('TmempsalpaySeeder selesai: '.Tmempsalpay::query()->count().' baris.');
    }
}