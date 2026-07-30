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
        Schema::create('JOBS', function (Blueprint $table) {
            $table->id();
            $table->string('QUEUE')->index();
            $table->longText('PAYLOAD');
            $table->unsignedSmallInteger('ATTEMPTS');
            $table->unsignedInteger('RESERVED_AT')->nullable();
            $table->unsignedInteger('AVAILABLE_AT');
            $table->unsignedInteger('CREATED_AT');
        });

        Schema::create('JOB_BATCHES', function (Blueprint $table) {
            $table->string('ID')->primary();
            $table->string('NAME');
            $table->integer('TOTAL_JOBS');
            $table->integer('PENDING_JOBS');
            $table->integer('FAILED_JOBS');
            $table->longText('FAILED_JOB_IDS');
            $table->mediumText('OPTIONS')->nullable();
            $table->integer('CANCELLED_AT')->nullable();
            $table->integer('CREATED_AT');
            $table->integer('FINISHED_AT')->nullable();
        });

        Schema::create('FAILED_JOBS', function (Blueprint $table) {
            $table->id();
            $table->string('UUID')->unique();
            $table->string('CONNECTION');
            $table->string('QUEUE');
            $table->longText('PAYLOAD');
            $table->longText('EXCEPTION');
            $table->timestamp('FAILED_AT')->useCurrent();

            $table->index(['CONNECTION', 'QUEUE', 'FAILED_AT']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('JOBS');
        Schema::dropIfExists('JOB_BATCHES');
        Schema::dropIfExists('FAILED_JOBS');
    }
};
