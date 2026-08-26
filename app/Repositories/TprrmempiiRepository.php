<?php

namespace App\Repositories;

use App\Models\Tprrmempii;

final class TprrmempiiRepository
{
    public function findByEmp(string $iEmp): ?Tprrmempii
    {
        return Tprrmempii::query()->where('i_emp', $iEmp)->first();
    }
}
