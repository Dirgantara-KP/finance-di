<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TMCONTR', function (Blueprint $table) {
            $table->string('I_CONTR', 50)->primary();
            $table->string('C_ORG_CONTR', 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TMCONTR');
    }
};
