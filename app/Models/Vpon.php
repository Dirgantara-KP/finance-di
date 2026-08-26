<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vpon extends Model
{
    protected $table = 'vpon';

    protected $fillable = [
        'c_pgm',
        'c_pgm_sub',
        'c_pgm_ver',
        'c_pgm_veract',
        'c_pgm_vergrp',
        'e_pgm_vergrp',
        'e_pgm',
        'c_org_core',
        'c_cost',
        'e_cost',
        'c_cost_hpp',
        'e_cost_hpp',
    ];
}
