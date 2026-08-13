<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'tmempsalpay', key: 'id', timestamps: false)]
#[Fillable([
    'd_proc_gaji',
    'c_org_cur',
    'i_inv_gaji',
    'c_bank_gaji',
    'c_emp_payloc',
    'v_emp_tunjgaji',
    'v_emp_potgaji',
])]
class Tmempsalpay extends Model
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
