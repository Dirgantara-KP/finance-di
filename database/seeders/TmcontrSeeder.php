<?php

namespace Database\Seeders;

use App\Models\Tmcontr;
use Illuminate\Database\Seeder;

class TmcontrSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/tmcontr.csv');

        if (! file_exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        Tmcontr::query()->truncate();

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, 0, ';');
        $rows = [];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            if (trim(implode('', $data)) === '') {
                continue;
            }

            $rows[] = [
                'i_id_contr' => (int) trim($data[0]),
                'c_org_contr' => trim($data[1]),
                'i_contr' => trim($data[2]),
                'i_contr_ref' => trim($data[3]),
                'n_contr_proj' => trim($data[4]),
            ];

            if (count($rows) >= 500) {
                Tmcontr::query()->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            Tmcontr::query()->insert($rows);
        }

        fclose($handle);

        $this->command->info('TmcontrSeeder selesai: '.Tmcontr::query()->count().' baris.');
    }
}
