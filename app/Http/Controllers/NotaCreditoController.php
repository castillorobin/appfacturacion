<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\DocumentoDTE;
use Illuminate\Support\Carbon;

class NotaCreditoController extends Controller
{
    public function emitirDesdeDTE(Request $request, DocumentoDTE $dte)
    {
       
            $original = json_decode(Storage::get($dte->json_legible_path), true);
            $montoParcial = round(floatval($request->input('monto', 0)) / 1.13, 2);
           //dd($monto);


            //$montoParcial = floatval($request->input('monto', 0)) - $monto;

          //  dd($montoParcial);
            //dd($montoParcial);
            /*
            dd($original);
            $fecha = now()->format('Y-m-d');
            $hora = now()->format('H:i:s');
            $codigoGeneracionNC = strtoupper(Str::uuid());

            // Permitir seleccionar si se emite nota total o parcial
            $montoParcial = floatval($request->input('monto', 0));
            $esTotal = $montoParcial === 0;

            $totalGravadaOriginal = floatval($original['resumen']['totalGravada'] ?? 0);
            $totalIvaOriginal = floatval($original['resumen']['tributos'][0]['valor'] ?? 0);
            $totalOriginal = $totalGravadaOriginal + $totalIvaOriginal;

            $base = $esTotal ? $totalGravadaOriginal : round($montoParcial / 1.13, 2);
            $iva = round($base * 0.13, 2);
            $total = $base + $iva;
*/
           
            return view('facturas.generardtenotacredito', compact('original', 'montoParcial'));

       
    }

    public function formEmitir(DocumentoDTE $dte)
    {
        return view('dtes.emitir', compact('dte'));
    }

     private function numeroALetras($numero)
{
    $unidad = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince',
        'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte'
    ];

    $decenas = [
        '', '', 'veinti', 'treinta', 'cuarenta', 'cincuenta',
        'sesenta', 'setenta', 'ochenta', 'noventa'
    ];

    $centenas = [
        '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos',
        'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
    ];

    if ($numero == 0) return 'Cero dólares 00/100';

    $entero = floor($numero);
    $centavos = round(($numero - $entero) * 100);

    $letras = '';

    if ($entero >= 1000000) {
        $millones = floor($entero / 1000000);
        $letras .= $this->numeroALetras($millones) . ' millón' . ($millones > 1 ? 'es' : '') . ' ';
        $entero %= 1000000;
    }

    if ($entero >= 1000) {
        $miles = floor($entero / 1000);
        if ($miles == 1) {
            $letras .= 'mil ';
        } else {
            $letras .= $this->numeroALetras($miles) . ' mil ';
        }
        $entero %= 1000;
    }

    if ($entero > 0) {
        if ($entero == 100) {
            $letras .= 'cien';
        } else {
            $c = floor($entero / 100);
            $d = floor(($entero % 100) / 10);
            $u = $entero % 10;

            $letras .= $centenas[$c];

            if ($d == 1 || ($d == 2 && $u == 0)) {
                $letras .= ($c > 0 ? ' ' : '') . $unidad[$d * 10 + $u];
            } elseif ($d == 2) {
                $letras .= 'i' . $unidad[$u];
            } elseif ($d > 2) {
                $letras .= ($c > 0 ? ' ' : '') . $decenas[$d];
                if ($u > 0) {
                    $letras .= ' y ' . $unidad[$u];
                }
            } elseif ($u > 0) {
                $letras .= ($c > 0 ? ' ' : '') . $unidad[$u];
            }
        }
    }

    $letras = trim(ucfirst($letras)) . ' dólares';
    $letras .= ' con ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100';

    return $letras;
}
    
}

// Asegúrate de tener disponible la función "numeroALetras" en algún helper o controlador.
