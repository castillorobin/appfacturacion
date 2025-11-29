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
        Schema::create('contadores_dte', function (Blueprint $table) {
             $table->id();
            $table->string('tipo_dte'); // por ejemplo: 01, 03, 05, etc.
            $table->unsignedBigInteger('numero_actual')->default(1);
            $table->timestamps();

            $table->unique('tipo_dte'); // Un contador por tipo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contadores_dte');
    }
};
