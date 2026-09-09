<?php

namespace Database\Seeders;

use App\Models\Vempsalpayemp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class VempsalpayempSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/vempsalpayemp.csv');

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
            if (count($data) < 11) {
                continue;
            }

            $rows[] = [
                'i_emp' => $data[0],
                'd_proc_gaji' => Carbon::parse($data[1])->toDateString(),
                'i_jour' => $data[2],
                'c_org_asal' => $data[3],
                'c_org_cur' => $data[4],
                'c_cost' => $data[5],
                'c_emp_pay' => $data[6],
                'c_bank_gaji' => $data[7],
                'c_emp_payloc' => $data[8],
                'v_emp_tunjgaji' => $data[9],
                'v_emp_potgaji' => $data[10],
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

        $this->command->info(
            'VempsalpayempSeeder selesai: '
            .Vempsalpayemp::query()->count()
            .' baris.'
        );
    }
}