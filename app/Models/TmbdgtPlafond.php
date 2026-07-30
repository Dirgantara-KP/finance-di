<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TmbdgtPlafond extends Model
{
    use SoftDeletes;

    protected $table = 'TMBDGTPLAFOND';

    protected $fillable = [
        'C_SOURCE',
        'C_ORG_ID',
        'C_ORG',
        'C_ORG_CONTR',
        'I_CONTR',
        'C_BDGT_CONTRSTAT',
        'C_BDGT_CONTRINEX',
        'C_BDGT_ANGGARAN',
        'C_PGM',
        'C_PGM_SUB',
        'C_PGM_VER',
        'C_COA_DR',
        'C_COA_CR',
        'C_CY',
        'V_BDGT_ADDMONTH1',
        'V_BDGT_ADDMONTH2',
        'V_BDGT_ADDMONTH3',
        'V_BDGT_ADDMONTH4',
        'V_BDGT_ADDMONTH5',
        'V_BDGT_ADDMONTH6',
        'V_BDGT_ADDMONTH7',
        'V_BDGT_ADDMONTH8',
        'V_BDGT_ADDMONTH9',
        'V_BDGT_ADDMONTH10',
        'V_BDGT_ADDMONTH11',
        'V_BDGT_ADDMONTH12',
        'V_BDGT_PLANTOTAL',
        'V_BDGT_ADDTOTAL',
        'V_BDGT_SALDOMONTH1',
        'V_BDGT_SALDOMONTH2',
        'V_BDGT_SALDOMONTH3',
        'V_BDGT_SALDOMONTH4',
        'V_BDGT_SALDOMONTH5',
        'V_BDGT_SALDOMONTH6',
        'V_BDGT_SALDOMONTH7',
        'V_BDGT_SALDOMONTH8',
        'V_BDGT_SALDOMONTH9',
        'V_BDGT_SALDOMONTH10',
        'V_BDGT_SALDOMONTH11',
        'V_BDGT_SALDOMONTH12',
        'V_BDGT_SALDOTOTAL',
        'I_ENTRY',
        'D_ENTRY',
        'C_ORG_CENTER',
    ];

    protected $casts = [
        'C_BDGT_ANGGARAN' => 'integer',
        'C_PGM_VER' => 'integer',
        'V_BDGT_ADDMONTH1' => 'integer',
        'V_BDGT_ADDMONTH2' => 'integer',
        'V_BDGT_ADDMONTH3' => 'integer',
        'V_BDGT_ADDMONTH4' => 'integer',
        'V_BDGT_ADDMONTH5' => 'integer',
        'V_BDGT_ADDMONTH6' => 'integer',
        'V_BDGT_ADDMONTH7' => 'integer',
        'V_BDGT_ADDMONTH8' => 'integer',
        'V_BDGT_ADDMONTH9' => 'integer',
        'V_BDGT_ADDMONTH10' => 'integer',
        'V_BDGT_ADDMONTH11' => 'integer',
        'V_BDGT_ADDMONTH12' => 'integer',
        'V_BDGT_PLANTOTAL' => 'integer',
        'V_BDGT_ADDTOTAL' => 'integer',
        'V_BDGT_SALDOMONTH1' => 'integer',
        'V_BDGT_SALDOMONTH2' => 'integer',
        'V_BDGT_SALDOMONTH3' => 'integer',
        'V_BDGT_SALDOMONTH4' => 'integer',
        'V_BDGT_SALDOMONTH5' => 'integer',
        'V_BDGT_SALDOMONTH6' => 'integer',
        'V_BDGT_SALDOMONTH7' => 'integer',
        'V_BDGT_SALDOMONTH8' => 'integer',
        'V_BDGT_SALDOMONTH9' => 'integer',
        'V_BDGT_SALDOMONTH10' => 'integer',
        'V_BDGT_SALDOMONTH11' => 'integer',
        'V_BDGT_SALDOMONTH12' => 'integer',
        'V_BDGT_SALDOTOTAL' => 'integer',
        'D_ENTRY' => 'datetime',
        'CREATED_AT' => 'datetime',
        'UPDATED_AT' => 'datetime',
        'DELETED_AT' => 'datetime',
    ];
}
