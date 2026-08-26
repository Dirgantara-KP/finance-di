<?php

namespace App\Repositories;

use App\Models\TmbdgtPlafond;
use App\Models\Trchartacct;
use Illuminate\Support\Facades\Cache;

final class TrchartacctRepository
{
    public function findByCost(string $cCost): ?Trchartacct
    {
        return Trchartacct::query()->where('c_cost', $cCost)->first();
    }

    /** @return array<string, string> */
    public function optionsForDropdown(): array
    {
        return Cache::remember('plafond:sandi_options', now()->addHour(), function () {
            $master = Trchartacct::query()
                ->where('c_cost_bsis', 'CC')
                ->where('c_cost_acctsub', '1')
                ->get()
                ->mapWithKeys(fn ($item) => [
                    $item->c_cost => $item->c_cost.' || '.$item->e_cost,
                ])
                ->toArray();

            $histori = TmbdgtPlafond::query()
                ->whereNotNull('c_coa_dr')
                ->distinct()
                ->pluck('c_coa_dr')
                ->mapWithKeys(fn ($code) => [
                    $code => $code.' || SANDI ANGGARAN',
                ])
                ->toArray();

            $merged = array_replace($histori, $master);
            ksort($merged);

            return $merged;
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('plafond:sandi_options');
    }
}
