<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'vempsalpayemp', key: 'id', timestamps: false)]
#[Fillable([
    'i_emp',
    'd_proc_gaji',
    'i_jour',
    'c_org_cur',
    'c_org_asal',
    'c_bank_gaji',
    'c_emp_payloc',
    'v_emp_tunjgaji',
    'v_emp_potgaji',
])]
class Vempsalpayemp extends Model
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