<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom yang dibutuhkan query INSERT INTO TMEMPSALPAY sesuai FD
     * item F. Kolom `c_cost`/`c_org_id`/`i_entry`/`d_entry` dibuat NULLABLE
     * karena baris lama hasil TmempsalpaySeeder belum punya nilai untuk
     * kolom ini — daripada mengarang nilai untuk data lama tersebut,
     * baris baru hasil fitur Insert nanti yang akan mengisinya dengan benar.
     */
    public function up(): void
    {
        Schema::table('tmempsalpay', function (Blueprint $table) {
            $table->string('c_cost', 10)->nullable()->after('c_emp_payloc');
            $table->char('c_cy', 3)->default('IDR')->after('c_cost');
            $table->decimal('v_emp_gaji', 15, 2)->default(0)->after('v_emp_potgaji');
            $table->string('c_org_id', 10)->nullable()->after('v_emp_gaji');
            $table->char('c_sal_paystat', 3)->default('-')->after('c_org_id');
            $table->string('c_org_payrecpt', 10)->default('-')->after('c_sal_paystat');
            $table->string('i_inv_payrecpt', 30)->default('-')->after('c_org_payrecpt');
            $table->date('d_inv_payrecpt')->nullable()->after('i_inv_payrecpt');
            $table->string('i_entry', 20)->nullable()->after('d_inv_payrecpt');
            $table->dateTime('d_entry')->nullable()->after('i_entry');
            $table->char('c_pot_paystat', 3)->default('-')->after('d_entry');
            $table->string('c_org_payrecpt1', 10)->default('-')->after('c_pot_paystat');
            $table->string('i_inv_payrecpt1', 30)->default('-')->after('c_org_payrecpt1');

            $table->index('c_org_id');
        });

        // Unique index setara Oracle IXUSALPAY1 ditambahkan di statement
        // terpisah: kalau tabel Anda kebetulan sudah punya baris duplikat
        // untuk kombinasi ini, ALTER TABLE ADD UNIQUE akan gagal dan Anda
        // akan tahu persis di titik mana harus bersihkan data dulu.
        Schema::table('tmempsalpay', function (Blueprint $table) {
            $table->unique(
                ['d_proc_gaji', 'c_bank_gaji', 'c_emp_payloc', 'c_org_cur', 'c_cost', 'i_inv_gaji'],
                'ux_tmempsalpay_1'
            );
        });
    }

    public function down(): void
    {
        Schema::table('tmempsalpay', function (Blueprint $table) {
            $table->dropUnique('ux_tmempsalpay_1');
            $table->dropIndex(['c_org_id']);
            $table->dropColumn([
                'c_cost', 'c_cy', 'v_emp_gaji', 'c_org_id', 'c_sal_paystat',
                'c_org_payrecpt', 'i_inv_payrecpt', 'd_inv_payrecpt', 'i_entry',
                'd_entry', 'c_pot_paystat', 'c_org_payrecpt1', 'i_inv_payrecpt1',
            ]);
        });
    }
};