<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documento_d_t_e_s', function (Blueprint $table) {
            
            $table->text('motivo_anulacion')->nullable();
            $table->date('fecha_anulacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_d_t_e_s', function (Blueprint $table) {
            //
        });
    }
};
