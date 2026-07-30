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
        Schema::create('USERS', function (Blueprint $table) {
            $table->id();
            $table->string('NAME');
            $table->string('EMAIL')->unique();
            $table->timestamp('EMAIL_VERIFIED_AT')->nullable();
            $table->string('PASSWORD');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('PASSWORD_RESET_TOKENS', function (Blueprint $table) {
            $table->string('EMAIL')->primary();
            $table->string('TOKEN');
            $table->timestamp('CREATED_AT')->nullable();
        });

        Schema::create('SESSIONS', function (Blueprint $table) {
            $table->string('ID')->primary();
            $table->foreignId('USER_ID')->nullable()->index();
            $table->string('IP_ADDRESS', 45)->nullable();
            $table->text('USER_AGENT')->nullable();
            $table->longText('PAYLOAD');
            $table->integer('LAST_ACTIVITY')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('USERS');
        Schema::dropIfExists('PASSWORD_RESET_TOKENS');
        Schema::dropIfExists('SESSIONS');
    }
};
