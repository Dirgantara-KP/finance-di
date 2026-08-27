<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thempsalref', function (Blueprint $table) {
            $table->id();

            $table->date('d_proc_gaji');
            $table->char('i_emp', 6);
            $table->char('c_org_cur', 6);
            $table->char('i_pgm', 2);
            $table->char('c_cost_cntl', 1);
            $table->char('c_emp_pay', 1);
            $table->char('c_emp_paylocket', 3);
            $table->char('c_emp_payloc', 3);
            $table->char('i_crnote_bankacct', 17);
            $table->char('c_data_status', 1);
            $table->char('i_jour', 14);
            $table->unsignedInteger('q_emp_actlhour')->default(0);
            $table->unsignedInteger('q_emp_ovthour')->default(0);
            $table->unsignedInteger('q_emp_actlhourbfr')->default(0);
            $table->unsignedInteger('q_emp_ovthourbfr')->default(0);
            $table->char('i_entry', 7);
            $table->dateTime('d_entry_time');
            $table->unsignedInteger('q_emp_losthour')->default(0);
            $table->string('c_cost', 3);
            $table->string('c_org_loan', 6);
            $table->decimal('v_emp_netgaji', 16, 2)->default(0);
            $table->string('c_org_vch', 6)->nullable();
            $table->string('i_inv_crvch', 16)->nullable();
            $table->string('c_org_payrecpt', 6)->nullable();
            $table->string('i_inv_payrecpt', 30)->nullable();
            $table->string('c_org_id', 2);

            // Setara Oracle IXAHEMPSALREF
            $table->index(['d_proc_gaji', 'c_org_cur', 'c_org_loan'], 'ix_ahempsalref');
            // Setara Oracle IXAHEMPSALREF02
            $table->index(['d_proc_gaji', 'i_emp', 'c_org_loan', 'i_jour'], 'ix_ahempsalref_02');
            // Setara Oracle IXFISP03 (unique)
            $table->unique(['d_proc_gaji', 'i_emp'], 'ux_fisp03');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thempsalref');
    }
};