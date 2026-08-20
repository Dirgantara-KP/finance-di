<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trorg extends Model
{
    protected $table = 'trorg';

    protected $fillable = [
        'c_org_cur',
        'n_org',
    ];
}
