<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dedupe: seeder dijalankan berulang -> banyak duplikat. Simpan row pertama (MIN id).
        // Key match unique yang akan dibuat: c_bdgt_anggaran, c_pgm, c_pgm_sub, c_pgm_ver, c_coa_dr, i_contr
        // (<=> = null-safe equal, 6 kolom ini nullable)
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                'DELETE t1 FROM tmbdgtplafond t1
                 INNER JOIN tmbdgtplafond t2
                 WHERE t1.id > t2.id
                   AND t1.c_bdgt_anggaran <=> t2.c_bdgt_anggaran
                   AND t1.c_org <=> t2.c_org
                   AND t1.c_org_id <=> t2.c_org_id
                   AND t1.c_pgm <=> t2.c_pgm
                   AND t1.c_pgm_sub <=> t2.c_pgm_sub
                   AND t1.c_pgm_ver <=> t2.c_pgm_ver
                   AND t1.c_coa_dr <=> t2.c_coa_dr
                   AND t1.i_contr <=> t2.i_contr'
            );
        }

        Schema::table('tmbdgtplafond', function (Blueprint $table) {
            $table->unique(
                ['c_bdgt_anggaran', 'c_org', 'c_org_id', 'c_pgm', 'c_pgm_sub', 'c_pgm_ver', 'c_coa_dr', 'i_contr'],
                'plafond_uniq_key'
            );
        });
    }

    public function down(): void
    {
        Schema::table('tmbdgtplafond', function (Blueprint $table) {
            $table->dropUnique('plafond_uniq_key');
        });
    }
};
