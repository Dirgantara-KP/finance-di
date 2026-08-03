<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tmcontr extends Model
{
    use SoftDeletes;

    protected $table = 'tmcontr';

    protected $fillable = [
        'i_id_contr',
        'c_org_contr',
        'i_contr',
        'i_contr_ref',
        'n_contr_proj',
    ];
}
