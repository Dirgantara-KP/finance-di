<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vpon extends Model
{
    use SoftDeletes;

    protected $table = 'VPON';

    protected $fillable = [
        'C_PGM',
        'C_PGM_SUB',
        'C_PGM_VER',
        'C_PGM_VERACT',
        'E_PGM',
        'C_ORG_CORE',
        'C_COST',
        'E_COST',
        'C_PGM_VERGRP',
        'E_PGM_VERGRP',
        'C_COST_HPP',
        'E_COST_HPP',
    ];

    protected $casts = [
        'CREATED_AT' => 'datetime',
        'UPDATED_AT' => 'datetime',
        'DELETED_AT' => 'datetime',
    ];
}
