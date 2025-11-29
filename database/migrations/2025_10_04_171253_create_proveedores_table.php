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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->enum('tipo_persona', ['natural', 'juridica']);
            $table->enum('tipo_contribuyente', ['pequeño', 'mediano', 'grande']);
            $table->string('pais');
            $table->string('departamento');
            $table->string('municipio');
            $table->string('telefono')->nullable();
            $table->string('giro')->nullable();
            $table->string('dui')->nullable();
            $table->string('nit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
