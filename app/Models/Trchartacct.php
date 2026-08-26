<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trchartacct extends Model
{
    protected $table = 'trchartacct';

    protected $fillable = [
        'c_cost_bsis',
        'c_cost_acctgrp',
        'c_cost_acctsub',
        'c_cost_acctsubgrp',
        'c_cost',
        'e_cost',
    ];
}
