<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $C_PGM_VER
 * @property string $E_PGM
 */
#[Table(name: 'VPON', key: 'id', timestamps: false)]
class VpOn extends Model
{
    protected $fillable = [
        'C_PGM',
        'C_PGM_SUB',
        'C_PGM_VER',
        'C_PGM_VERACT',
        'C_COST_HPP',
        'E_PGM',
    ];
}
