<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'tprrmempii', key: 'id', timestamps: false)]
#[Fillable(['i_emp', 'n_emp'])]
class Tprrmempii extends Model
{
    //
}
