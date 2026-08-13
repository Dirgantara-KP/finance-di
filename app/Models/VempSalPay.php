<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'vempsalpay', key: 'id', timestamps: false)]
#[Fillable([
    'd_proc_gaji',
    'c_org_cur',
    'i_jour',
    'c_bank_gaji',
    'c_emp_payloc',
])]
class Vempsalpay extends Model
{
    protected function casts(): array
    {
        return [
            'd_proc_gaji' => 'date',
        ];
    }
}
