<?php

namespace App\Repositories;

use App\Models\Vororg;
use Illuminate\Support\Facades\Cache;

final class VororgRepository
{
    /** @return array<string, string> */
    public function optionsForDropdown(): array
    {
        return Cache::remember('plafond:org_options', now()->addHour(), function () {
            return Vororg::query()
                ->whereNotNull('c_org_cur')
                ->where('c_org_assetstat', 'OPN')
                ->orderBy('c_org_cur')
                ->get()
                ->mapWithKeys(fn ($item) => [
                    $item->c_org_cur => $item->c_org_cur.' || '.$item->n_org_cur,
                ])
                ->toArray();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('plafond:org_options');
    }
}
