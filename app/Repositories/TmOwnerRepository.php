<?php

namespace App\Repositories;

use App\Models\TmOwner;
use Illuminate\Support\Collection;

class TmOwnerRepository
{
   
    public function findOtorisator(): Collection
    {
        return TmOwner::query()
            ->select(['i_emp_own1', 'n_emp_own1', 'e_pos_own1'])
            ->where('c_trans', 'TRS')
            ->where('c_org_id', 'CO')
            ->orderBy('i_emp_own1')
            ->get();
    }

   
    public function findOriginator(): Collection
    {
        return TmOwner::query()
            ->select(['i_emp_own2', 'n_emp_own2'])
            ->where('c_trans', 'UPH')
            ->where('c_org_id', 'CO')
            ->orderBy('i_emp_own2')
            ->get();
    }
}