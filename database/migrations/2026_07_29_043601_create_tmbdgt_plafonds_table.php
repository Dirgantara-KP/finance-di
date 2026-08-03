<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tmbdgtplafond', function (Blueprint $table) {
            $table->id();

            $table->char('c_source', 2);
            $table->char('c_org_id', 2);
            $table->char('c_org', 6);
            $table->char('c_org_contr', 6);
            $table->string('i_contr', 100);
            $table->string('c_bdgt_contrstat', 5)->nullable();
            $table->string('c_bdgt_contrinex', 5)->nullable();
            $table->integer('c_bdgt_anggaran')->nullable();

            $table->char('c_pgm', 2)->nullable();
            $table->char('c_pgm_sub', 3)->nullable();
            $table->integer('c_pgm_ver')->nullable();

            $table->char('c_coa_dr', 3)->nullable();
            $table->char('c_coa_cr', 3)->nullable();
            $table->string('c_cy', 5)->nullable();

            $table->bigInteger('v_bdgt_addmonth1')->default(0);
            $table->bigInteger('v_bdgt_addmonth2')->default(0);
            $table->bigInteger('v_bdgt_addmonth3')->default(0);
            $table->bigInteger('v_bdgt_addmonth4')->default(0);
            $table->bigInteger('v_bdgt_addmonth5')->default(0);
            $table->bigInteger('v_bdgt_addmonth6')->default(0);
            $table->bigInteger('v_bdgt_addmonth7')->default(0);
            $table->bigInteger('v_bdgt_addmonth8')->default(0);
            $table->bigInteger('v_bdgt_addmonth9')->default(0);
            $table->bigInteger('v_bdgt_addmonth10')->default(0);
            $table->bigInteger('v_bdgt_addmonth11')->default(0);
            $table->bigInteger('v_bdgt_addmonth12')->default(0);
            $table->bigInteger('v_bdgt_plantotal')->default(0);
            $table->bigInteger('v_bdgt_addtotal')->default(0);

            $table->bigInteger('v_bdgt_saldomonth1')->default(0);
            $table->bigInteger('v_bdgt_saldomonth2')->default(0);
            $table->bigInteger('v_bdgt_saldomonth3')->default(0);
            $table->bigInteger('v_bdgt_saldomonth4')->default(0);
            $table->bigInteger('v_bdgt_saldomonth5')->default(0);
            $table->bigInteger('v_bdgt_saldomonth6')->default(0);
            $table->bigInteger('v_bdgt_saldomonth7')->default(0);
            $table->bigInteger('v_bdgt_saldomonth8')->default(0);
            $table->bigInteger('v_bdgt_saldomonth9')->default(0);
            $table->bigInteger('v_bdgt_saldomonth10')->default(0);
            $table->bigInteger('v_bdgt_saldomonth11')->default(0);
            $table->bigInteger('v_bdgt_saldomonth12')->default(0);
            $table->bigInteger('v_bdgt_saldototal')->default(0);

            $table->string('i_entry', 20)->nullable();
            $table->dateTime('d_entry')->nullable();
            $table->char('c_org_center', 6)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tmbdgtplafond');
    }
};
