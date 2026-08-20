<?php

namespace App\Repositories;

use App\Models\Trorg;
use Illuminate\Support\Facades\Cache;

final class TrorgRepository
{
    public function findByOrgCur(string $cOrgCur): ?Trorg
    {
        return Trorg::query()->firstWhere('c_org_cur', $cOrgCur);
    }

    /** @return array<string, string> */
    public function optionsForDropdown(): array
    {
        return Cache::remember('gaji:org_options', now()->addHour(), function () {
            return Trorg::query()
                ->orderBy('c_org_cur')
                ->get()
                ->mapWithKeys(fn ($item) => [
                    $item->c_org_cur => $item->c_org_cur.' || '.$item->n_org,
                ])
                ->toArray();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('gaji:org_options');
    }
}
