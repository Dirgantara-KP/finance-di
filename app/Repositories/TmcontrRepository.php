<?php

namespace App\Repositories;

use App\Models\TmbdgtPlafond;
use App\Models\Tmcontr;
use App\Models\Vororg;
use Illuminate\Support\Facades\Cache;

final class TmcontrRepository
{
    public function findByContr(string $iContr, string $cOrgContr): ?Tmcontr
    {
        // 1. Cari langsung berdasarkan i_contr dan c_org_contr
        $contr = Tmcontr::query()
            ->where('i_contr', $iContr)
            ->where('c_org_contr', $cOrgContr)
            ->first();

        if ($contr) {
            return $contr;
        }

        // 2. Jika tidak ditemukan, cari melalui Direktorat Induk di Vororg
        $vororg = Vororg::query()
            ->where('c_org_cur', $cOrgContr)
            ->first();

        if ($vororg && $vororg->c_org_direktorat) {
            $contr = Tmcontr::query()
                ->where('i_contr', $iContr)
                ->where('c_org_contr', $vororg->c_org_direktorat)
                ->first();

            if ($contr) {
                return $contr;
            }
        }

        // 3. Fallback: cari berdasarkan i_contr saja
        return Tmcontr::query()
            ->where('i_contr', $iContr)
            ->first();
    }

    /** @return array<string, string> */
    public function kontrakOptionsFor(string $orgContr): array
    {
        return Cache::remember("plafond:kontrak:{$orgContr}", now()->addHour(), function () use ($orgContr) {
            // 1. Cek langsung dengan c_org_contr = $orgContr
            $kontrak = Tmcontr::query()
                ->where('c_org_contr', $orgContr)
                ->pluck('i_contr', 'i_contr')
                ->toArray();

            // 2. Jika kosong, cari berdasarkan c_org_direktorat dari Vororg (misal AK2300 -> KU0000)
            if (empty($kontrak)) {
                $vororg = Vororg::query()
                    ->where('c_org_cur', $orgContr)
                    ->first();

                if ($vororg && $vororg->c_org_direktorat) {
                    $kontrak = Tmcontr::query()
                        ->where('c_org_contr', $vororg->c_org_direktorat)
                        ->pluck('i_contr', 'i_contr')
                        ->toArray();
                }
            }

            // 3. Fallback: jika tetap kosong, ambil dari TMCONTR
            if (empty($kontrak)) {
                $kontrak = Tmcontr::query()
                    ->pluck('i_contr', 'i_contr')
                    ->toArray();
            }

            // 4. Sertakan juga nomor kontrak historis dari TMBDGTPLAFOND (misal "Operasional Thn 2000")
            $histori = TmbdgtPlafond::query()
                ->where('c_org', $orgContr)
                ->orWhere('c_org_contr', $orgContr)
                ->pluck('i_contr', 'i_contr')
                ->toArray();

            if (empty($histori)) {
                $histori = TmbdgtPlafond::query()->pluck('i_contr', 'i_contr')->toArray();
            }

            return array_unique(array_merge($kontrak, $histori));
        });
    }

    public static function flushCache(?string $orgContr = null): void
    {
        if ($orgContr) {
            Cache::forget("plafond:kontrak:{$orgContr}");
        } else {
            Cache::flush();
        }
    }
}
