<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('VRORG', function (Blueprint $table) {
            $table->string('C_ORG_CUR', 20)->primary();
            $table->string('N_ORG_CUR', 100);
            $table->string('C_ORG_ASSETSTAT', 10)->default('OPN');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('VRORG');
    }
};
