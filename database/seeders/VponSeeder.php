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

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, 0, ';');
        $rows = [];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $rows[] = [
                'c_pgm' => trim($data[0]),
                'c_pgm_sub' => trim($data[1]),
                'c_pgm_ver' => trim($data[2]),
                'c_pgm_veract' => trim($data[9]),
                'c_pgm_vergrp' => trim($data[7]),
                'e_pgm' => trim($data[3]),
                'c_org_core' => trim($data[4]),
                'c_cost' => trim($data[5]),
                'e_cost' => trim($data[6]),
                'e_pgm_vergrp' => trim($data[8]),
                'c_cost_hpp' => trim($data[10]),
                'e_cost_hpp' => trim($data[11]),
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
