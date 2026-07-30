<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vrorg', function (Blueprint $table) {
            $table->id();

            $table->string('i_org', 20);
            $table->integer('i_org_ut');
            $table->integer('i_org_dir');
            $table->char('i_org_subdir', 2);
            $table->char('i_org_div', 2);
            $table->char('i_org_subdiv', 2);
            $table->char('i_org_dept', 2);
            $table->char('i_org_subdept', 2);
            $table->char('i_org_bid', 2);
            $table->char('i_org_subbid', 2);
            $table->char('i_org_00', 2);
            $table->integer('c_org_statlvl');
            $table->char('c_org_cur', 6);
            $table->char('c_org_parent', 6)->nullable();
            $table->char('c_org_div', 6)->nullable();
            $table->char('c_org_subdir', 6)->nullable();
            $table->char('c_org_direktorat', 6);
            $table->string('n_org_cur');
            $table->string('n_org_cur_short')->nullable();
            $table->string('n_org_english')->nullable();
            $table->string('n_org_shortenglish')->nullable();
            $table->string('n_org_direktorat')->nullable();
            $table->string('n_org_direktorat_short')->nullable();
            $table->integer('i_emp_mngr')->nullable();
            $table->string('n_emp')->nullable();
            $table->char('c_org_assetstat', 3)->nullable();
            $table->date('d_org_start')->nullable();
            $table->date('d_org_finish')->nullable();
            $table->char('c_pos_grpf', 3)->nullable();
            $table->string('n_pos_title')->nullable();
            $table->char('c_pos_grp', 3)->nullable();
            $table->string('n_pos_titlestrukt')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vrorg');
    }
};
