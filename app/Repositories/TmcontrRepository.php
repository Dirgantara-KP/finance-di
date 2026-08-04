<?php

namespace App\Repositories;

use App\Models\Tmcontr;
use Illuminate\Support\Facades\Cache;

final class TmcontrRepository
{
    public function findByContr(string $iContr, string $cOrgContr): ?Tmcontr
    {
        return Tmcontr::query()->firstWhere([
            'i_contr' => $iContr,
            'c_org_contr' => $cOrgContr,
        ]);
    }

    /** @return array<string, string> */
    public function kontrakOptionsFor(string $orgContr): array
    {
        return Cache::remember("plafond:kontrak:{$orgContr}", now()->addHour(), function () use ($orgContr) {
            return Tmcontr::query()
                ->where('c_org_contr', $orgContr)
                ->pluck('i_contr', 'i_contr')
                ->toArray();
        });
    }

    public static function flushCache(?string $orgContr = null): void
    {
        if ($orgContr) {
            Cache::forget("plafond:kontrak:{$orgContr}");
        } else {
            // Flush all kontrak cache — keys not tagged, use Cache::tags if switching to Redis
            // ponytail: linear scan acceptable for small dataset, flush all when org unknown
            $orgs = Tmcontr::query()->distinct()->pluck('c_org_contr')->toArray();
            foreach ($orgs as $org) {
                Cache::forget("plafond:kontrak:{$org}");
            }
        }
    }
}
