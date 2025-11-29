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
        Schema::create('kardexes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->date('fecha');
            $table->string('tipo'); // Compra, Venta, Ajuste, etc.
            $table->string('documento')->nullable();
            $table->string('descripcion')->nullable();
            $table->decimal('Eunidad', 10, 2);
            $table->decimal('Ecosto', 10, 2);
            $table->decimal('Sunidad', 10, 2);
            $table->decimal('Scosto', 10, 2);
            $table->decimal('Tunidad', 10, 2);
            $table->decimal('Tcostop', 10, 2);
            $table->decimal('saldo', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardexes');
    }
};
