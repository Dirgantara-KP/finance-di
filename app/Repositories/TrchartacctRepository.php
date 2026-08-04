<?php

namespace App\Repositories;

use App\Models\Trchartacct;
use Illuminate\Support\Facades\Cache;

final class TrchartacctRepository
{
    public function findByCost(string $cCost): ?Trchartacct
    {
        return Trchartacct::query()->firstWhere('c_cost', $cCost);
    }

    /** @return array<string, string> */
    public function optionsForDropdown(): array
    {
        return Cache::remember('plafond:sandi_options', now()->addHour(), function () {
            return Trchartacct::query()
                ->where('c_cost_bsis', 'CC')
                ->where('c_cost_acctsub', '1')
                ->get()
                ->mapWithKeys(fn ($item) => [
                    $item->c_cost => $item->c_cost.' || '.$item->e_cost,
                ])
                ->toArray();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('plafond:sandi_options');
    }
}
