<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $C_ORG_CONTR
 * @property string $I_CONTR
 */
#[Table(name: 'TMCONTR', key: 'I_CONTR', timestamps: false)]
class TmContr extends Model
{
    protected $fillable = [
        'I_CONTR',
        'C_ORG_CONTR',
    ];
}
