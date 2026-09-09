<?php

namespace Database\Seeders;

use App\Models\Tmowner;
use Illuminate\Database\Seeder;

class TmownerSeeder extends Seeder
{
    
    public function run(): void
    {
        $path = database_path('seeders/data/tmowner.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = [
                'c_trans' => $data[0],
                'c_org_id' => $data[1],
                'i_emp_own1' => $data[2] !== '' ? $data[2] : null,
                'n_emp_own1' => $data[3] !== '' ? $data[3] : null,
                'e_pos_own1' => $data[4] !== '' ? $data[4] : null,
                'i_emp_own2' => $data[5] !== '' ? $data[5] : null,
                'n_emp_own2' => $data[6] !== '' ? $data[6] : null,
            ];
        }

        fclose($handle);

        if (! empty($rows)) {
            Tmowner::query()->insert($rows);
        }

        $this->command->info('TmownerSeeder selesai: '.Tmowner::query()->count().' baris.');
    }
}