<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tmcontr', function (Blueprint $table) {
            $table->id();

            $table->integer('i_id_contr');
            $table->char('c_org_contr', 6);
            $table->string('i_contr');
            $table->string('i_contr_ref');
            $table->string('n_contr_proj');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tmcontr');
    }
};
