<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'trorg', key: 'id', timestamps: false)]
#[Fillable([
    'c_org_cur',
    'n_org',
])]
class Trorg extends Model
{
    //
}
