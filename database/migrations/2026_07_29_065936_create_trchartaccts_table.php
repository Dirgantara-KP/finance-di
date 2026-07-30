<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trchartacct', function (Blueprint $table) {
            $table->id();

            $table->char('c_cost_bsis', 2);
            $table->integer('c_cost_acctgrp');
            $table->char('c_cost_acctsub', 3)->nullable();
            $table->integer('c_cost_acctsubgrp');
            $table->char('c_cost', 3);
            $table->string('e_cost');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trchartacct');
    }
};
