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
            $table->decimal('v_emp_tunjgaji', 15, 2)->default(0);
            $table->decimal('v_emp_potgaji', 15, 2)->default(0);

            $table->index('d_proc_gaji');
            $table->index('c_org_cur');
            $table->index('c_bank_gaji');
            $table->index('c_emp_payloc');
            $table->index('i_inv_gaji');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tmempsalpay');
    }
};