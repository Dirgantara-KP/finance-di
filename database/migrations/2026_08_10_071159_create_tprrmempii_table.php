<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tprrmempii', function (Blueprint $table) {
            $table->id();
            $table->string('i_emp', 10)->unique();
            $table->string('n_emp', 100);

            $table->index('i_emp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tprrmempii');
    }
};
