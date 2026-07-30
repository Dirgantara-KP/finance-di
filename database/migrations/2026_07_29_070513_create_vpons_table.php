<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('VPON', function (Blueprint $table) {
            $table->id();

            $table->char('C_PGM', 2);
            $table->char('C_PGM_SUB', 3);
            $table->char('C_PGM_VER', 3);
            $table->char('C_PGM_VERACT', 3)->nullable();
            $table->string('E_PGM');
            $table->char('C_ORG_CORE', 2);
            $table->char('C_COST', 3)->nullable();
            $table->string('E_COST')->nullable();
            $table->char('C_PGM_VERGRP', 3)->nullable();
            $table->string('E_PGM_VERGRP')->nullable();
            $table->char('C_COST_HPP', 3)->nullable();
            $table->string('E_COST_HPP')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('VPON');
    }
};
