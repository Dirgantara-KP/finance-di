<?php

namespace Database\Seeders;

use App\Models\Vpon;
use Illuminate\Database\Seeder;

class VponSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/vpon.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        Vpon::query()->truncate();

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, 0, ';');
        $rows = [];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $str = fn (int $i): ?string => isset($data[$i]) && trim($data[$i]) !== '' && trim($data[$i]) !== '-' ? trim($data[$i]) : null;

            $rows[] = [
                'c_pgm' => trim($data[0]),
                'c_pgm_sub' => trim($data[1]),
                'c_pgm_ver' => trim($data[2]),
                'c_pgm_veract' => $str(9) ?? 'OPN',
                'c_pgm_vergrp' => $str(7),
                'e_pgm' => trim($data[3]),
                'c_org_core' => trim($data[4]),
                'c_cost' => $str(5),
                'e_cost' => $str(6),
                'e_pgm_vergrp' => $str(8),
                'c_cost_hpp' => $str(10),
                'e_cost_hpp' => $str(11),
            ];

            if (count($rows) >= 500) {
                Vpon::query()->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            Vpon::query()->insert($rows);
        }

        fclose($handle);

        $this->command->info('VponSeeder selesai: '.Vpon::query()->count().' baris.');
    }
}
