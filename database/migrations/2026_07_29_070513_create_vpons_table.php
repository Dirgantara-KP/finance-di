<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vpon', function (Blueprint $table) {
            $table->id();

            $table->char('c_pgm', 2);
            $table->char('c_pgm_sub', 3);
            $table->char('c_pgm_ver', 3);
            $table->char('c_pgm_veract', 3)->nullable();
            $table->string('e_pgm');
            $table->char('c_org_core', 2);
            $table->char('c_cost', 3)->nullable();
            $table->string('e_cost')->nullable();
            $table->char('c_pgm_vergrp', 3)->nullable();
            $table->string('e_pgm_vergrp')->nullable();
            $table->char('c_cost_hpp', 3)->nullable();
            $table->string('e_cost_hpp')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vpon');
    }
};
