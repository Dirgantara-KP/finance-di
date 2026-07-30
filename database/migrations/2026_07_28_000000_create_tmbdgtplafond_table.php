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

            $table->string('C_SOURCE', 10);
            $table->string('C_ORG_ID', 20);
            $table->string('C_ORG', 20);
            $table->string('C_ORG_CONTR', 20);
            $table->string('I_CONTR', 50);
            $table->string('C_BDGT_CONTRSTAT', 10);
            $table->string('C_BDGT_CONTRINEX', 10);
            $table->string('C_BDGT_ANGGARAN', 4);
            $table->string('C_PGM', 20);
            $table->string('C_PGM_SUB', 20);
            $table->string('C_PGM_VER', 20);
            $table->string('C_COA_DR', 20);
            $table->string('C_COA_CR', 10);
            $table->string('C_CY', 5);

            $table->decimal('V_BDGT_ADDMONTH1', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH2', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH3', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH4', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH5', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH6', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH7', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH8', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH9', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH10', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH11', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDMONTH12', 15, 2)->default(0);
            $table->decimal('V_BDGT_ADDTOTAL', 15, 2)->default(0);
            $table->decimal('V_BDGT_PLANTOTAL', 15, 2)->default(0);

            $table->decimal('V_BDGT_SALDOMONTH1', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH2', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH3', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH4', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH5', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH6', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH7', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH8', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH9', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH10', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH11', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOMONTH12', 15, 2)->default(0);
            $table->decimal('V_BDGT_SALDOTOTAL', 15, 2)->default(0);

            $table->string('I_ENTRY', 50);
            $table->timestamp('D_ENTRY')->useCurrent();
            $table->string('C_ORG_CENTER', 20);
            $table->softDeletes();

            $table->unique(
                ['C_ORG', 'I_CONTR', 'C_PGM_VER', 'C_COA_DR', 'C_BDGT_ANGGARAN'],
                'uk_plafond_composite'
            );
            $table->index('C_BDGT_ANGGARAN');
            $table->index('C_ORG');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TMBDGTPLAFOND');
    }
};
