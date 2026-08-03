<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $C_COST
 * @property string $E_COST
 */
#[Table(name: 'TRCHARTACCT', key: 'C_COST', timestamps: false)]
class TrChartAcct extends Model
{
    protected $fillable = [
        'C_COST',
        'C_COST_BSIS',
        'C_COST_ACCTGRP',
        'C_COST_ACCTSUB',
        'C_COST_ACCTSUBGRP',
        'E_COST',
    ];
}
