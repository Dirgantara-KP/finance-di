<?php

namespace Database\Seeders;

use App\Models\Trchartacct;
use Illuminate\Database\Seeder;

class TrchartacctSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/trchartacct.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, 0, ';');
        $rows = [];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $rows[] = [
                'c_cost_bsis' => trim($data[0]),
                'c_cost_acctgrp' => trim($data[1]),
                'c_cost_acctsub' => trim($data[2]),
                'c_cost_acctsubgrp' => trim($data[3]),
                'c_cost' => trim($data[4]),
                'e_cost' => trim($data[5]),
            ];

            if (count($rows) >= 500) {
                Trchartacct::query()->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            Trchartacct::query()->insert($rows);
        }

        fclose($handle);

        $this->command->info('TrchartacctSeeder selesai: '.Trchartacct::query()->count().' baris.');
    }
}
