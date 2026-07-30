<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TMCONTR', function (Blueprint $table) {
            $table->id();

            $table->integer('I_ID_CONTR');
            $table->char('C_ORG_CONTR', 6);
            $table->string('I_CONTR');
            $table->string('I_CONTR_REF');
            $table->string('N_CONTR_PROJ');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TMCONTR');
    }
};
