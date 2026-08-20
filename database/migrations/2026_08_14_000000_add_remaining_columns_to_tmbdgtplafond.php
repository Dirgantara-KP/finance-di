<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tmbdgtplafond', function (Blueprint $table) {
            for ($i = 1; $i <= 12; $i++) {
                $table->bigInteger("v_bdgt_month{$i}")->default(0);
            }
            $table->bigInteger('v_bdgt_total')->default(0);
            $table->char('c_org_dit', 6)->nullable();
            $table->string('c_bdgt_cat', 10)->nullable();
            for ($i = 1; $i <= 12; $i++) {
                $table->bigInteger("v_bdgt_planmonth{$i}")->default(0);
            }
            $table->string('c_stat_bdgt', 5)->nullable();
            $table->string('i_ref_updbdgt', 20)->nullable();
            $table->string('d_ref_updbdgt', 30)->nullable();
            $table->string('c_bdgt_stat', 5)->nullable();
            $table->string('i_pmn', 20)->nullable();
            $table->string('d_pmn', 30)->nullable();
            $table->string('i_order', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tmbdgtplafond', function (Blueprint $table) {
            $cols = [];
            for ($i = 1; $i <= 12; $i++) {
                $cols[] = "v_bdgt_month{$i}";
                $cols[] = "v_bdgt_planmonth{$i}";
            }
            $cols = [
                ...$cols,
                'v_bdgt_total',
                'c_org_dit',
                'c_bdgt_cat',
                'c_stat_bdgt',
                'i_ref_updbdgt',
                'd_ref_updbdgt',
                'c_bdgt_stat',
                'i_pmn',
                'd_pmn',
                'i_order',
            ];
            $table->dropColumn($cols);
        });
    }
};
