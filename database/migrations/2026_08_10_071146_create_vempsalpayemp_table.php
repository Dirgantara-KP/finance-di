<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vempsalpayemp', function (Blueprint $table) {
            $table->id();

            $table->string('i_emp', 10);
            $table->date('d_proc_gaji');
            $table->string('i_jour', 20);

            $table->string('c_org_asal', 10);
            $table->string('c_org_cur', 10);

            $table->char('c_cost', 5)->nullable();
            $table->unsignedTinyInteger('c_emp_pay')->nullable();

            $table->string('c_bank_gaji', 10);
            $table->string('c_emp_payloc', 10);

            $table->decimal('v_emp_tunjgaji', 15, 2)->default(0);
            $table->decimal('v_emp_potgaji', 15, 2)->default(0);

            $table->index('i_emp');
            $table->index('d_proc_gaji');
            $table->index('i_jour');
            $table->index('c_org_cur');
            $table->index('c_org_asal');
            $table->index('c_cost');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vempsalpayemp');
    }
};