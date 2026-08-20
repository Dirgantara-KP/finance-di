<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vempsalpay', function (Blueprint $table) {
            $table->id();

            $table->date('d_proc_gaji');
            $table->char('c_org_cur', 6);
            $table->string('i_jour', 50);
            $table->char('c_bank_gaji', 4);
            $table->char('c_emp_payloc', 5);

            $table->index(['d_proc_gaji', 'i_jour'], 'idx_vempsalpay_proses_bukti');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vempsalpay');
    }
};
