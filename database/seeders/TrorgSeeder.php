<?php

namespace Database\Seeders;

use App\Models\Trorg;
use Illuminate\Database\Seeder;

class TrorgSeeder extends Seeder
{
    
    public function run(): void
    {
        $path = database_path('seeders/data/trorg.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = [
                'c_org_cur' => $data[0],
                'n_org' => $data[1],
            ];
        }

        fclose($handle);

        if (! empty($rows)) {
            Trorg::query()->insert($rows);
        }

        $this->command->info('TrorgSeeder selesai: '.Trorg::query()->count().' baris.');
    }
}