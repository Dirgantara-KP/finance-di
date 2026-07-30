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
        Schema::table('tmbdgtplafond', function (Blueprint $table) {
            $table->string('c_pgm_ver', 3)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tmbdgtplafond', function (Blueprint $table) {
            $table->integer('c_pgm_ver')->nullable()->change();
        });
    }
};
