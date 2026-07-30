<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trchartacct extends Model
{
    use SoftDeletes;

    protected $table = 'trchartacct';

    protected $fillable = [
        'c_cost_bsis',
        'c_cost_acctgrp',
        'c_cost_acctsub',
        'c_cost_acctsubgrp',
        'c_cost',
        'e_cost',
    ];

    protected function casts(): array
    {
        return [
            'c_cost_acctgrp' => 'integer',
            'c_cost_acctsubgrp' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
