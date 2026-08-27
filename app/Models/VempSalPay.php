<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'vempsalpay', key: 'id', timestamps: false)]
#[Fillable([
    'd_proc_gaji',
    'c_org_echl',
    'c_org_cur',
    'i_jour',
    'c_bank_gaji',
    'c_emp_payloc',
    'c_cost',
    'v_emp_tunjgaji',
    'v_emp_potgaji',
])]
class Vempsalpay extends Model
{
    protected function casts(): array
    {
        return [
            'd_proc_gaji' => 'date',
            'v_emp_tunjgaji' => 'decimal:2',
            'v_emp_potgaji' => 'decimal:2',
        ];
    }
}