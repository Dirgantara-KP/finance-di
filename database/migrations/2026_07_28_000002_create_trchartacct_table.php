<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TRCHARTACCT', function (Blueprint $table) {
            $table->string('C_COST', 20)->primary();
            $table->string('C_COST_BSIS', 10);
            $table->string('C_COST_ACCTGRP', 20)->nullable();
            $table->string('C_COST_ACCTSUB', 20)->nullable();
            $table->string('C_COST_ACCTSUBGRP', 20)->nullable();
            $table->string('E_COST', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TRCHARTACCT');
    }
};
