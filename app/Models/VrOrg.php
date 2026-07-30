<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'VRORG', key: 'C_ORG_CUR', timestamps: false)]
class VrOrg extends Model
{
    protected $fillable = [
        'C_ORG_CUR',
        'N_ORG_CUR',
        'C_ORG_ASSETSTAT',
    ];
}
