<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vpon extends Model
{
    use SoftDeletes;

    protected $table = 'vpon';

    protected $fillable = [
        'c_pgm',
        'c_pgm_sub',
        'c_pgm_ver',
        'c_pgm_veract',
        'e_pgm',
        'c_org_core',
        'c_cost',
        'e_cost',
        'c_pgm_vergrp',
        'e_pgm_vergrp',
        'c_cost_hpp',
        'e_cost_hpp',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
