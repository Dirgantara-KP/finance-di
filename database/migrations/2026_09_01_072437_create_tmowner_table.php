<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tmowner', function (Blueprint $table) {
            $table->id();

            
            $table->string('c_trans', 3);
            $table->string('c_org_id', 2);

            // Data Otorisator
            $table->string('i_emp_own1', 10)->nullable();
            $table->string('n_emp_own1', 100)->nullable();
            $table->string('e_pos_own1', 100)->nullable();

            // Data Originator
            $table->string('i_emp_own2', 10)->nullable();
            $table->string('n_emp_own2', 100)->nullable();

            $table->index(['c_trans', 'c_org_id'], 'ix_tmowner_trans_org');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tmowner');
    }
};