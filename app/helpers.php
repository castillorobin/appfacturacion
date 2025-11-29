<?php

use App\Models\Caja;
use Illuminate\Support\Facades\Auth;
use App\Models\ContadorDTE;

if (!function_exists('obtenerCajaAbiertaUsuario')) {
    function obtenerCajaAbiertaUsuario()
    {
        return Caja::where('user_id', Auth::id())
            ->whereNull('fecha_cierre')
            ->latest()
            ->first();
    }
}

if (!function_exists('obtenerNumeroControlDTE')) {
    function obtenerNumeroControlDTE($tipoDte)
    {
        $contador = ContadorDTE::firstOrCreate(
            ['tipo_dte' => $tipoDte],
            ['numero_actual' => 1]
        );

        $numero = $contador->numero_actual;

        // Aumentar en 1
        $contador->increment('numero_actual');

        return str_pad($numero, 15, '0', STR_PAD_LEFT); // ejemplo: 00000001
    }
}