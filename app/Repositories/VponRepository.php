<?php

namespace App\Repositories;

use App\Models\TmbdgtPlafond;
use App\Models\Vpon;
use Illuminate\Support\Facades\Cache;

final class VponRepository
{
    public function findByVersion(string $cPgmVer): ?Vpon
    {
        // data vpon bisa duplikat antar seed — ambil row terbaru (id terbesar) biar deterministik
        return Vpon::query()
            ->where('c_pgm_ver', $cPgmVer)
            ->latest('id')
            ->first();
    }

    /** @return array<string, string> */
    public function optionsForDropdown(): array
    {
        return Cache::remember('plafond:pon_options', now()->addHour(), function () {
            // match legacy SQL: SELECT C_PGM, C_PGM_SUB, C_PGM_VER, C_PGM_VERACT, C_COST_HPP, E_PGM FROM VPON WHERE C_PGM_VERACT ='OPN'
            $master = Vpon::query()
                ->select(['c_pgm', 'c_pgm_sub', 'c_pgm_ver', 'c_pgm_veract', 'c_cost_hpp', 'e_pgm'])
                ->where('c_pgm_veract', 'OPN')
                ->latest('id')
                ->get()
                // dedupe seed ganda: unik per c_pgm_ver, pakai row terbaru (sudah diurut id desc)
                ->unique('c_pgm_ver')
                // label spesifik: program-subprogram-versi supaya tiap pilihan jelas 1 row
                ->mapWithKeys(fn ($item) => [
                    $item->c_pgm_ver => $item->c_pgm.'-'.$item->c_pgm_sub.'-'.$item->c_pgm_ver.' || '.$item->e_pgm,
                ])
                ->toArray();

            $histori = TmbdgtPlafond::query()
                ->whereNotNull('c_pgm_ver')
                ->distinct()
                ->pluck('c_pgm_ver')
                ->mapWithKeys(fn ($code) => [
                    $code => $code.' || DOKUMEN PON HISTORIS',
                ])
                ->toArray();

            $merged = array_replace($histori, $master);
            ksort($merged);

            return $merged;
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('plafond:pon_options');
    }
}
