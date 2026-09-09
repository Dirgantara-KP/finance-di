<?php

namespace Database\Seeders;

use App\Models\Vempsalpay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class VempsalpaySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/vempsalpay.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->command->error("Gagal membuka file: {$path}");

            return;
        }

        fgetcsv($handle);

        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 10) {
                continue;
            }

            $rows[] = [
                'd_proc_gaji' => Carbon::parse($data[0])->toDateString(),
                'i_jour' => $data[1],
                'c_bank_gaji' => $data[2],
                'c_emp_payloc' => $data[3],
                'c_org_echl' => $data[4],
                'c_org_cur' => $data[5],
                'v_emp_tunjgaji' => $data[6],
                'v_emp_potgaji' => $data[7],
                'v_gaji_bersih' => $data[8],
                'c_cost' => $data[9],
            ];

            if (count($rows) >= 500) {
                Vempsalpay::query()->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            Vempsalpay::query()->insert($rows);
        }

        fclose($handle);

        $this->command->info(
            'VempsalpaySeeder selesai: '
            .Vempsalpay::query()->count()
            .' baris.'
        );
    }
}