<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tmcontr extends Model
{
    use SoftDeletes;

    protected $table = 'TMCONTR';

    protected $fillable = [
        'I_ID_CONTR',
        'C_ORG_CONTR',
        'I_CONTR',
        'I_CONTR_REF',
        'N_CONTR_PROJ',
    ];

    protected $casts = [
        'CREATED_AT' => 'datetime',
        'UPDATED_AT' => 'datetime',
        'DELETED_AT' => 'datetime',
    ];
}
