<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vororg extends Model
{
    use SoftDeletes;

    protected $table = 'vrorg';

    protected $fillable = [
        'i_org',
        'i_org_ut',
        'i_org_dir',
        'i_org_subdir',
        'i_org_div',
        'i_org_subdiv',
        'i_org_dept',
        'i_org_subdept',
        'i_org_bid',
        'i_org_subbid',
        'i_org_00',
        'c_org_statlvl',
        'c_org_cur',
        'c_org_parent',
        'c_org_div',
        'c_org_subdir',
        'c_org_direktorat',
        'n_org_cur',
        'n_org_cur_short',
        'n_org_english',
        'n_org_shortenglish',
        'n_org_direktorat',
        'n_org_direktorat_short',
        'i_emp_mngr',
        'n_emp',
        'c_org_assetstat',
        'd_org_start',
        'd_org_finish',
        'c_pos_grpf',
        'n_pos_title',
        'c_pos_grp',
        'n_pos_titlestrukt',
    ];

    protected function casts(): array
    {
        return [
            'i_org_ut' => 'integer',
            'i_org_dir' => 'integer',
            'c_org_statlvl' => 'integer',
            'i_emp_mngr' => 'integer',
            'd_org_start' => 'date',
            'd_org_finish' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
