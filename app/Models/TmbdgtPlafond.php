<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table(name: 'TMBDGTPLAFOND', key: 'id', timestamps: false)]
#[Fillable([
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
    'V_BDGT_ADDTOTAL',
    'V_BDGT_PLANTOTAL',
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
    'C_ORG_CENTER',
])]
class TmbdgtPlafond extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'D_ENTRY' => 'datetime',
            'V_BDGT_ADDMONTH1' => 'decimal:2',
            'V_BDGT_ADDMONTH2' => 'decimal:2',
            'V_BDGT_ADDMONTH3' => 'decimal:2',
            'V_BDGT_ADDMONTH4' => 'decimal:2',
            'V_BDGT_ADDMONTH5' => 'decimal:2',
            'V_BDGT_ADDMONTH6' => 'decimal:2',
            'V_BDGT_ADDMONTH7' => 'decimal:2',
            'V_BDGT_ADDMONTH8' => 'decimal:2',
            'V_BDGT_ADDMONTH9' => 'decimal:2',
            'V_BDGT_ADDMONTH10' => 'decimal:2',
            'V_BDGT_ADDMONTH11' => 'decimal:2',
            'V_BDGT_ADDMONTH12' => 'decimal:2',
            'V_BDGT_ADDTOTAL' => 'decimal:2',
            'V_BDGT_PLANTOTAL' => 'decimal:2',
            'V_BDGT_SALDOMONTH1' => 'decimal:2',
            'V_BDGT_SALDOMONTH2' => 'decimal:2',
            'V_BDGT_SALDOMONTH3' => 'decimal:2',
            'V_BDGT_SALDOMONTH4' => 'decimal:2',
            'V_BDGT_SALDOMONTH5' => 'decimal:2',
            'V_BDGT_SALDOMONTH6' => 'decimal:2',
            'V_BDGT_SALDOMONTH7' => 'decimal:2',
            'V_BDGT_SALDOMONTH8' => 'decimal:2',
            'V_BDGT_SALDOMONTH9' => 'decimal:2',
            'V_BDGT_SALDOMONTH10' => 'decimal:2',
            'V_BDGT_SALDOMONTH11' => 'decimal:2',
            'V_BDGT_SALDOMONTH12' => 'decimal:2',
            'V_BDGT_SALDOTOTAL' => 'decimal:2',
        ];
    }
}
