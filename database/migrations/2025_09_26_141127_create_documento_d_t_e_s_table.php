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
        Schema::create('documento_d_t_e_s', function (Blueprint $table) {
             $table->id();
            $table->string('sello_recibido')->nullable();
    $table->uuid('codigo_generacion')->nullable();
    $table->string('numero_control')->nullable();
    $table->string('factura')->nullable();
    $table->dateTime('fecha_generacion')->nullable();
$table->string('tipo_dte')->nullable();
// Rutas a artefactos
$table->string('json_firmado_path')->nullable(); // JWS/JWT devuelto por el firmador/MH
$table->string('json_original_path')->nullable(); // JSON plano antes de firmar
$table->string('json_legible_path')->nullable(); // JSON "para contador" con sello/firma agregados
$table->string('pdf_path')->nullable(); // Versión legible en PDF
$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_d_t_e_s');
    }
};
