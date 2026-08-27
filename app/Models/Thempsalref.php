<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thempsalref extends Model
{
    protected $table = 'thempsalref';

    protected $fillable = [
        'd_proc_gaji',
        'i_emp',
        'c_org_cur',
        'i_pgm',
        'c_cost_cntl',
        'c_emp_pay',
        'c_emp_paylocket',
        'c_emp_payloc',
        'i_crnote_bankacct',
        'c_data_status',
        'i_jour',
        'q_emp_actlhour',
        'q_emp_ovthour',
        'q_emp_actlhourbfr',
        'q_emp_ovthourbfr',
        'i_entry',
        'd_entry_time',
        'q_emp_losthour',
        'c_cost',
        'c_org_loan',
        'v_emp_netgaji',
        'c_org_vch',
        'i_inv_crvch',
        'c_org_payrecpt',
        'i_inv_payrecpt',
        'c_org_id',
    ];

    protected $casts = [
        'd_proc_gaji' => 'date',
        'd_entry_time' => 'datetime',
        'v_emp_netgaji' => 'decimal:2',
    ];

    public $timestamps = false;
}