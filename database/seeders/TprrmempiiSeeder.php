<?php

namespace Database\Seeders;

use App\Models\Tprrmempii;
use Illuminate\Database\Seeder;

class TprrmempiiSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/tprrmempii.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = [
                'i_emp' => $data[0],
                'n_emp' => $data[1],
            ];
        }

        fclose($handle);

        if (! empty($rows)) {
            Tprrmempii::query()->insert($rows);
        }

        $this->command->info('TprrmempiiSeeder selesai: '.Tprrmempii::query()->count().' baris.');
    }
}
