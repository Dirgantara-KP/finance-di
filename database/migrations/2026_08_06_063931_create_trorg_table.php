<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trorg', function (Blueprint $table) {
            $table->id();
            $table->string('c_org_cur', 10)->unique();
            $table->string('n_org', 100);

            $table->index('c_org_cur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trorg');
    }
};
