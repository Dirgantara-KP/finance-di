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

        Trchartacct::query()->truncate();

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, 0, ';');
        $rows = [];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $str = fn (int $i): ?string => isset($data[$i]) && trim($data[$i]) !== '' ? trim($data[$i]) : null;

            $rows[] = [
                'c_cost_bsis' => trim($data[0]),
                'c_cost_acctgrp' => (int) trim($data[1]),
                'c_cost_acctsub' => $str(2),
                'c_cost_acctsubgrp' => (int) trim($data[3]),
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
