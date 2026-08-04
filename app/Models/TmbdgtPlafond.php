<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TmbdgtPlafond extends Model
{
    use SoftDeletes;

    protected $table = 'tmbdgtplafond';
    /**
     * @param Builder<Model> $q
     */
    public function scopeForKontrak(Builder $q, string $orgContr, string $iContr): Builder
    {
        return $q->where('c_org_contr', $orgContr)->where('i_contr', $iContr);
    }

    protected $fillable = [
        'c_source',
        'c_org_id',
        'c_org',
        'c_org_contr',
        'i_contr',
        'c_bdgt_contrstat',
        'c_bdgt_contrinex',
        'c_bdgt_anggaran',
        'c_pgm',
        'c_pgm_sub',
        'c_pgm_ver',
        'c_coa_dr',
        'c_coa_cr',
        'c_cy',
        'v_bdgt_addmonth1',
        'v_bdgt_addmonth2',
        'v_bdgt_addmonth3',
        'v_bdgt_addmonth4',
        'v_bdgt_addmonth5',
        'v_bdgt_addmonth6',
        'v_bdgt_addmonth7',
        'v_bdgt_addmonth8',
        'v_bdgt_addmonth9',
        'v_bdgt_addmonth10',
        'v_bdgt_addmonth11',
        'v_bdgt_addmonth12',
        'v_bdgt_plantotal',
        'v_bdgt_addtotal',
        'v_bdgt_saldomonth1',
        'v_bdgt_saldomonth2',
        'v_bdgt_saldomonth3',
        'v_bdgt_saldomonth4',
        'v_bdgt_saldomonth5',
        'v_bdgt_saldomonth6',
        'v_bdgt_saldomonth7',
        'v_bdgt_saldomonth8',
        'v_bdgt_saldomonth9',
        'v_bdgt_saldomonth10',
        'v_bdgt_saldomonth11',
        'v_bdgt_saldomonth12',
        'v_bdgt_saldototal',
        'i_entry',
        'd_entry',
        'c_org_center',
    ];

    protected function casts(): array
    {
        return [
            'c_bdgt_anggaran' => 'integer',
            'c_pgm_ver' => 'string',
            'v_bdgt_addmonth1' => 'integer',
            'v_bdgt_addmonth2' => 'integer',
            'v_bdgt_addmonth3' => 'integer',
            'v_bdgt_addmonth4' => 'integer',
            'v_bdgt_addmonth5' => 'integer',
            'v_bdgt_addmonth6' => 'integer',
            'v_bdgt_addmonth7' => 'integer',
            'v_bdgt_addmonth8' => 'integer',
            'v_bdgt_addmonth9' => 'integer',
            'v_bdgt_addmonth10' => 'integer',
            'v_bdgt_addmonth11' => 'integer',
            'v_bdgt_addmonth12' => 'integer',
            'v_bdgt_plantotal' => 'integer',
            'v_bdgt_addtotal' => 'integer',
            'v_bdgt_saldomonth1' => 'integer',
            'v_bdgt_saldomonth2' => 'integer',
            'v_bdgt_saldomonth3' => 'integer',
            'v_bdgt_saldomonth4' => 'integer',
            'v_bdgt_saldomonth5' => 'integer',
            'v_bdgt_saldomonth6' => 'integer',
            'v_bdgt_saldomonth7' => 'integer',
            'v_bdgt_saldomonth8' => 'integer',
            'v_bdgt_saldomonth9' => 'integer',
            'v_bdgt_saldomonth10' => 'integer',
            'v_bdgt_saldomonth11' => 'integer',
            'v_bdgt_saldomonth12' => 'integer',
            'v_bdgt_saldototal' => 'integer',
            'd_entry' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
