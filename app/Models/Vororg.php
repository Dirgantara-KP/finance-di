<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vororg extends Model
{
    use SoftDeletes;

    protected $table = 'VRORG';

    protected $fillable = [
        'I_ORG',
        'I_ORG_UT',
        'I_ORG_DIR',
        'I_ORG_SUBDIR',
        'I_ORG_DIV',
        'I_ORG_SUBDIV',
        'I_ORG_DEPT',
        'I_ORG_SUBDEPT',
        'I_ORG_BID',
        'I_ORG_SUBBID',
        'I_ORG_00',
        'C_ORG_STATLVL',
        'C_ORG_CUR',
        'C_ORG_PARENT',
        'C_ORG_DIV',
        'C_ORG_SUBDIR',
        'C_ORG_DIREKTORAT',
        'N_ORG_CUR',
        'N_ORG_CUR_SHORT',
        'N_ORG_ENGLISH',
        'N_ORG_SHORTENGLISH',
        'N_ORG_DIREKTORAT',
        'N_ORG_DIREKTORAT_SHORT',
        'I_EMP_MNGR',
        'N_EMP',
        'C_ORG_ASSETSTAT',
        'D_ORG_START',
        'D_ORG_FINISH',
        'C_POS_GRPF',
        'N_POS_TITLE',
        'C_POS_GRP',
        'N_POS_TITLESTRUKT',
    ];

    protected $casts = [
        'I_ORG_UT' => 'integer',
        'I_ORG_DIR' => 'integer',
        'C_ORG_STATLVL' => 'integer',
        'I_EMP_MNGR' => 'integer',
        'D_ORG_START' => 'date',
        'D_ORG_FINISH' => 'date',
        'CREATED_AT' => 'datetime',
        'UPDATED_AT' => 'datetime',
        'DELETED_AT' => 'datetime',
    ];
}
