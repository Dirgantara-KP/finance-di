<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TRCHARTACCT', function (Blueprint $table) {
            $table->id();

            $table->char('C_COST_BSIS', 2);
            $table->integer('C_COST_ACCTGRP');
            $table->char('C_COST_ACCTSUB', 3)->nullable();
            $table->integer('C_COST_ACCTSUBGRP');
            $table->char('C_COST', 3);
            $table->string('E_COST');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TRCHARTACCT');
    }
};
