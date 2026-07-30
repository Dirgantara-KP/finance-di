<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('VRORG', function (Blueprint $table) {
            $table->id();

            $table->string('I_ORG', 20);
            $table->integer('I_ORG_UT');
            $table->integer('I_ORG_DIR');
            $table->char('I_ORG_SUBDIR', 2);
            $table->char('I_ORG_DIV', 2);
            $table->char('I_ORG_SUBDIV', 2);
            $table->char('I_ORG_DEPT', 2);
            $table->char('I_ORG_SUBDEPT', 2);
            $table->char('I_ORG_BID', 2);
            $table->char('I_ORG_SUBBID', 2);
            $table->char('I_ORG_00', 2);
            $table->integer('C_ORG_STATLVL');
            $table->char('C_ORG_CUR', 6);
            $table->char('C_ORG_PARENT', 6)->nullable();
            $table->char('C_ORG_DIV', 6)->nullable();
            $table->char('C_ORG_SUBDIR', 6)->nullable();
            $table->char('C_ORG_DIREKTORAT', 6);
            $table->string('N_ORG_CUR');
            $table->string('N_ORG_CUR_SHORT')->nullable();
            $table->string('N_ORG_ENGLISH')->nullable();
            $table->string('N_ORG_SHORTENGLISH')->nullable();
            $table->string('N_ORG_DIREKTORAT')->nullable();
            $table->string('N_ORG_DIREKTORAT_SHORT')->nullable();
            $table->integer('I_EMP_MNGR')->nullable();
            $table->string('N_EMP')->nullable();
            $table->char('C_ORG_ASSETSTAT', 3)->nullable();
            $table->date('D_ORG_START')->nullable();
            $table->date('D_ORG_FINISH')->nullable();
            $table->char('C_POS_GRPF', 3)->nullable();
            $table->string('N_POS_TITLE')->nullable();
            $table->char('C_POS_GRP', 3)->nullable();
            $table->string('N_POS_TITLESTRUKT')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('VRORG');
    }
};
