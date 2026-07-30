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
            $table->string('C_PGM', 20);
            $table->string('C_PGM_SUB', 20)->nullable();
            $table->string('C_PGM_VER', 20)->unique();
            $table->string('C_PGM_VERACT', 10)->default('OPN');
            $table->string('C_COST_HPP', 20)->nullable();
            $table->string('E_PGM', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('VPON');
    }
};
