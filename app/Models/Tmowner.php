<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'tmowner', key: 'id', timestamps: false)]
#[Fillable([
    'c_trans',
    'c_org_id',
    'i_emp_own1',
    'n_emp_own1',
    'e_pos_own1',
    'i_emp_own2',
    'n_emp_own2',
])]
class Tmowner extends Model
{
    //
}