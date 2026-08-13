<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TrchartacctSeeder::class,
            VponSeeder::class,
            TmcontrSeeder::class,
            VororgSeeder::class,
            TmbdgtPlafondSeeder::class,
            TrorgSeeder::class,
            TmempsalpaySeeder::class,
            VempsalpaySeeder::class,
            VempsalpayempSeeder::class,
            TprrmempiiSeeder::class,
        ]);
    }
}
