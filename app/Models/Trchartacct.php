<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trchartacct extends Model
{
    use SoftDeletes;

    protected $table = 'TRCHARTACCT';

    protected $fillable = [
        'C_COST_BSIS',
        'C_COST_ACCTGRP',
        'C_COST_ACCTSUB',
        'C_COST_ACCTSUBGRP',
        'C_COST',
        'E_COST',
    ];

    protected $casts = [
        'C_COST_ACCTGRP' => 'integer',
        'C_COST_ACCTSUBGRP' => 'integer',
        'CREATED_AT' => 'datetime',
        'UPDATED_AT' => 'datetime',
        'DELETED_AT' => 'datetime',
    ];
}
