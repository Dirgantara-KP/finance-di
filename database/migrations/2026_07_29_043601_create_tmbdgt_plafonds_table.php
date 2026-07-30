<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TMBDGTPLAFOND', function (Blueprint $table) {
            $table->id();

            $table->char('C_SOURCE', 2);
            $table->char('C_ORG_ID', 2);
            $table->char('C_ORG', 6);
            $table->char('C_ORG_CONTR', 6);
            $table->string('I_CONTR', 100);
            $table->string('C_BDGT_CONTRSTAT', 5)->nullable();
            $table->string('C_BDGT_CONTRINEX', 5)->nullable();
            $table->integer('C_BDGT_ANGGARAN')->nullable();

            $table->char('C_PGM', 2)->nullable();
            $table->char('C_PGM_SUB', 3)->nullable();
            $table->integer('C_PGM_VER')->nullable();

            $table->char('C_COA_DR', 3)->nullable();
            $table->char('C_COA_CR', 3)->nullable();
            $table->string('C_CY', 5)->nullable();

            $table->bigInteger('V_BDGT_ADDMONTH1')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH2')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH3')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH4')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH5')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH6')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH7')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH8')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH9')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH10')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH11')->default(0);
            $table->bigInteger('V_BDGT_ADDMONTH12')->default(0);
            $table->bigInteger('V_BDGT_PLANTOTAL')->default(0);
            $table->bigInteger('V_BDGT_ADDTOTAL')->default(0);

            $table->bigInteger('V_BDGT_SALDOMONTH1')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH2')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH3')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH4')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH5')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH6')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH7')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH8')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH9')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH10')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH11')->default(0);
            $table->bigInteger('V_BDGT_SALDOMONTH12')->default(0);
            $table->bigInteger('V_BDGT_SALDOTOTAL')->default(0);

            $table->string('I_ENTRY', 20)->nullable();
            $table->dateTime('D_ENTRY')->nullable();
            $table->char('C_ORG_CENTER', 6)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TMBDGTPLAFOND');
    }
};
