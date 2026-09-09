<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tmempsalpay', function (Blueprint $table) {
            $table->id();

            $table->date('d_proc_gaji');
            $table->string('c_org_cur', 10);
            $table->string('i_inv_gaji', 20);
            $table->string('c_bank_gaji', 10);
            $table->string('c_emp_payloc', 10);

            $table->string('c_cost', 10)->nullable();
            $table->char('c_cy', 3)->default('IDR');

            $table->decimal('v_emp_tunjgaji', 15, 2)->default(0);
            $table->decimal('v_emp_potgaji', 15, 2)->default(0);
            $table->decimal('v_emp_gaji', 15, 2)->default(0);

            $table->string('c_org_id', 10)->nullable();
            $table->char('c_sal_paystat', 3)->default('-');
            $table->string('c_org_payrecpt', 10)->default('-');
            $table->string('i_inv_payrecpt', 30)->default('-');
            $table->date('d_inv_payrecpt')->nullable();

            $table->string('i_entry', 20)->nullable();
            $table->dateTime('d_entry')->nullable();

            $table->char('c_pot_paystat', 3)->default('-');
            $table->string('c_org_payrecpt1', 10)->default('-');
            $table->string('i_inv_payrecpt1', 30)->default('-');

            $table->index('d_proc_gaji');
            $table->index('c_org_cur');
            $table->index('c_bank_gaji');
            $table->index('c_emp_payloc');
            $table->index('i_inv_gaji');
            $table->index('c_org_id');

            $table->unique(
                [
                    'd_proc_gaji',
                    'c_bank_gaji',
                    'c_emp_payloc',
                    'c_org_cur',
                    'c_cost',
                    'i_inv_gaji',
                ],
                'ux_tmempsalpay_1'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tmempsalpay');
    }
};