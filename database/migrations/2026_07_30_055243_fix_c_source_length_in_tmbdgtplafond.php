<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tmbdgtplafond', function (Blueprint $table) {
            $table->char('c_source', 3)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tmbdgtplafond', function (Blueprint $table) {
            $table->char('c_source', 2)->change();
        });
    }
};
