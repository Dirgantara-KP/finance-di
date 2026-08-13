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
        $header = fgetcsv($handle);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = [
                'd_proc_gaji' => Carbon::parse($data[0])->toDateString(),
                'c_org_cur' => $data[1],
                'i_jour' => $data[2],
                'c_bank_gaji' => $data[3],
                'c_emp_payloc' => $data[4],
            ];

            // Insert per 500 baris supaya tidak terlalu berat sekali jalan.
            if (count($rows) >= 500) {
                Vempsalpay::query()->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            Vempsalpay::query()->insert($rows);
        }

        fclose($handle);

        $this->command->info('VempsalpaySeeder selesai: '.Vempsalpay::query()->count().' baris.');
    }
}
