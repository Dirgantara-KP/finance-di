<?php

namespace Database\Seeders;

use App\Models\Tmempsalpay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TmempsalpaySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/tmempsalpay.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        Tmempsalpay::query()->truncate();

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = [
                'd_proc_gaji' => Carbon::parse(trim($data[0]))->toDateString(),
                'c_org_cur' => trim($data[1]),
                'i_inv_gaji' => trim($data[2]),
                'c_bank_gaji' => trim($data[3]),
                'c_emp_payloc' => trim($data[4]),
                'v_emp_tunjgaji' => trim($data[5]) !== '' ? trim($data[5]) : 0,
                'v_emp_potgaji' => trim($data[6]) !== '' ? trim($data[6]) : 0,
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
