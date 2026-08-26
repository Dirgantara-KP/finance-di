<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tabel yang kolom deleted_at-nya dihapus (model sudah tanpa SoftDeletes). */
    private array $tables = ['tmbdgtplafond', 'tmcontr', 'trchartacct', 'vpon', 'vrorg'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('deleted_at');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->timestamp('deleted_at')->nullable();
            });
        }
    }
};
