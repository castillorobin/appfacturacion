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
        Schema::create('contingencia_d_t_e_s', function (Blueprint $table) {
             $table->id();
            $table->string('tipo_dte', 2);
            $table->string('numero_control');
            $table->uuid('codigo_generacion');
            $table->string('json_original_path');
            $table->enum('estado', ['pendiente', 'reportado', 'enviado'])->default('pendiente');
            $table->timestamp('fecha_generacion');
            $table->string('cliente');
            $table->decimal('total', 10, 2);
            $table->string('motivo')->nullable();
            $table->string('factura')->nullable();
            $table->tinyInteger('tipo_modelo')->default(2); // 2 = contingencia
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contingencia_d_t_e_s');
    }
};
