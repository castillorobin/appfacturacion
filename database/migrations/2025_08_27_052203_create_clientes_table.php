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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nit')->nullable();
            $table->string('dui')->nullable();
            $table->string('nrc')->nullable();
            $table->string('telefono')->nullable();
            $table->integer('actividad_economica_id')->nullable();
            $table->integer('departamento_id')->nullable();
            $table->integer('municipio_id')->nullable();
            $table->string('correo')->nullable();
            $table->text('direccion')->nullable();
            $table->string('nombre_comercial')->nullable();
            
          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
