<?php

namespace Database\Seeders;

use App\Models\VempSalPayEmp;
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

        VempSalPayEmp::query()->truncate();

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            // Pastikan jumlah kolom sesuai dengan CSV terbaru
            if (count($data) < 11) {
                $this->command->warn(
                    'Baris dilewati karena jumlah kolom kurang dari 11: '
                    .implode(',', $data)
                );

                continue;
            }

            $rows[] = [
                'i_emp' => trim($data[0]),
                'd_proc_gaji' => Carbon::parse(trim($data[1]))->toDateString(),
                'i_jour' => trim($data[2]),
                'c_org_asal' => trim($data[3]),
                'c_org_cur' => trim($data[4]),
                'c_cost' => trim($data[5]),
                'c_emp_pay' => trim($data[6]),
                'c_bank_gaji' => trim($data[7]),
                'c_emp_payloc' => trim($data[8]),
                'v_emp_tunjgaji' => trim($data[9]) !== '' ? trim($data[9]) : 0,
                'v_emp_potgaji' => trim($data[10]) !== '' ? trim($data[10]) : 0,
            ];

            if (count($rows) >= 500) {
                VempSalPayEmp::query()->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            VempSalPayEmp::query()->insert($rows);
        }

        fclose($handle);

        $this->command->info(
            'VempsalpayempSeeder selesai: '
            .VempSalPayEmp::query()->count()
            .' baris.'
        );
    }
}
