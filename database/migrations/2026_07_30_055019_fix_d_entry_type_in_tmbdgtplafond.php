<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tmbdgtplafond', function (Blueprint $table) {
            $table->string('d_entry', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tmbdgtplafond', function (Blueprint $table) {
            $table->dateTime('d_entry')->nullable()->change();
        });
    }
};
