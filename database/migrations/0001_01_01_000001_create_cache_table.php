<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('CACHE', function (Blueprint $table) {
            $table->string('KEY')->primary();
            $table->mediumText('VALUE');
            $table->bigInteger('EXPIRATION')->index();
        });

        Schema::create('CACHE_LOCKS', function (Blueprint $table) {
            $table->string('KEY')->primary();
            $table->string('OWNER');
            $table->bigInteger('EXPIRATION')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CACHE');
        Schema::dropIfExists('CACHE_LOCKS');
    }
};
