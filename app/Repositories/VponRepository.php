<?php

namespace App\Repositories;

use App\Models\Vpon;
use Illuminate\Support\Facades\Cache;

final class VponRepository
{
    public function findByVersion(string $cPgmVer): ?Vpon
    {
        return Vpon::query()->firstWhere('c_pgm_ver', $cPgmVer);
    }

    /** @return array<string, string> */
    public function optionsForDropdown(): array
    {
        return Cache::remember('plafond:pon_options', now()->addHour(), function () {
            return Vpon::query()
                ->where('c_pgm_veract', 'OPN')
                ->get()
                ->mapWithKeys(fn ($item) => [
                    $item->c_pgm_ver => $item->c_pgm_ver.' || '.$item->e_pgm,
                ])
                ->toArray();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('plafond:pon_options');
    }
}
