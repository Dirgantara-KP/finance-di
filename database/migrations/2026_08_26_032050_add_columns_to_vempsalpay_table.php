<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom yang dibutuhkan query FD point E (Rekap Cost Center dari
     * data mentah, saat kombinasi filter belum ada di TMEMPSALPAY) dan
     * query INSERT FD point F (sumber data yang diagregasi ke TMEMPSALPAY).
     *
     * Kolom dibuat NULLABLE karena tabel `vempsalpay` sudah ada isinya dari
     * migration/seeder sebelumnya — menambah kolom NOT NULL tanpa default
     * di atas tabel yang sudah berisi data akan gagal. Setelah migration
     * ini jalan, jalankan ulang VempsalpaySeeder (lihat STEP 14) supaya
     * nilainya terisi, bukan NULL.
     */
    public function up(): void
    {
        Schema::table('vempsalpay', function (Blueprint $table) {
            $table->char('c_org_echl', 2)->nullable()->after('d_proc_gaji');
            $table->char('c_cost', 3)->nullable()->after('c_emp_payloc');
            $table->decimal('v_emp_tunjgaji', 15, 2)->default(0)->after('c_cost');
            $table->decimal('v_emp_potgaji', 15, 2)->default(0)->after('v_emp_tunjgaji');

            $table->index('c_org_echl');
            $table->index('c_cost');
        });
    }

    public function down(): void
    {
        Schema::table('vempsalpay', function (Blueprint $table) {
            $table->dropIndex(['c_org_echl']);
            $table->dropIndex(['c_cost']);
            $table->dropColumn(['c_org_echl', 'c_cost', 'v_emp_tunjgaji', 'v_emp_potgaji']);
        });
    }
};