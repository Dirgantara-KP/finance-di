<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tmcontr extends Model
{
    protected $table = 'tmcontr';

    protected $fillable = [
        'i_id_contr',
        'c_org_contr',
        'i_contr',
        'i_contr_ref',
        'n_contr_proj',
    ];
}
