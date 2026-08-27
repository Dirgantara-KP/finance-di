<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tmempsalpay extends Model
{
    protected $table = 'tmempsalpay';

    protected $fillable = [
        'd_proc_gaji',
        'c_org_cur',
        'i_inv_gaji',
        'c_bank_gaji',
        'c_emp_payloc',
        'c_cost',
        'c_cy',
        'v_emp_tunjgaji',
        'v_emp_potgaji',
        'v_emp_gaji',
        'c_org_id',
        'c_sal_paystat',
        'c_org_payrecpt',
        'i_inv_payrecpt',
        'd_inv_payrecpt',
        'i_entry',
        'd_entry',
        'c_pot_paystat',
        'c_org_payrecpt1',
        'i_inv_payrecpt1',
    ];

    protected $casts = [
        'd_proc_gaji' => 'date',
        'v_emp_tunjgaji' => 'decimal:2',
        'v_emp_potgaji' => 'decimal:2',
        'v_emp_gaji' => 'decimal:2',
        'd_inv_payrecpt' => 'date',
        'd_entry' => 'datetime',
    ];

    public $timestamps = false;
}