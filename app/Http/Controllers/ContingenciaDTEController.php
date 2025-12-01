<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContingenciaDTE;
use Illuminate\Support\Facades\Storage;
use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Models\Cliente;
use App\Models\Producto;
use App\Models\MovimientoCaja;
use App\Models\Proveedor;

use Illuminate\Support\Facades\DB;
use App\Services\DTEService;
use App\Models\Actividad;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str; 


class ContingenciaDTEController extends Controller
{
    public function index()
    {
        $dtes = ContingenciaDTE::orderByDesc('created_at')->get();
        return view('contingencia.index', compact('dtes'));
    }

    public function reportar(Request $request, $id)
    {
        $dte = ContingenciaDTE::findOrFail($id);
        $original = json_decode(Storage::get($dte->json_original_path), true);
        //dd($original);

        /*
        $dte = ContingenciaDTE::findOrFail($id);
        $dte->estado = 'reportado';
        $dte->save();
        */
        if ($original['identificacion']['tipoDte'] == '03') {
           
            return view('contingencia.reportardteccf', compact('original'));
        }
        if ($original['identificacion']['tipoDte'] == '01') {
            return view('contingencia.reportardteconsumidor', compact('original'));
        }
        

       // return redirect()->back()->with('success', 'DTE marcado como reportado.');
    }

    public function enviar($id)
    {
         $dte = ContingenciaDTE::findOrFail($id);
        $original = json_decode(Storage::get($dte->json_original_path), true);
        //dd($original);

        /*
        $dte = ContingenciaDTE::findOrFail($id);
        $dte->estado = 'reportado';
        $dte->save();
        */
        if ($original['identificacion']['tipoDte'] == '03') {
            return view('contingencia.enviardteccf', compact('original'));
        }
        if ($original['identificacion']['tipoDte'] == '01') {
            return view('contingencia.enviardteconsumidor', compact('original'));
        }
        
    }

    public function crearcontingencia()
    {
        $clientes = Cliente::all();
        $productos = Producto::all();
        return view('contingencia.crear', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    { $factura = null;

    $request->validate([
        'cliente_id' => 'required',
        'tipo' => 'required',
        'productos_json' => 'required|string',
    ]);

    DB::transaction(function () use ($request, &$factura) {

        $cliente = Cliente::findOrFail($request->cliente_id);

        $factura = Factura::create([
            'cliente_id' => $cliente->id,
            'tipo' => $request->tipo,
            'fecha' => now(),
            'numero' => 'F' . str_pad(Factura::max('id') + 1, 6, '0', STR_PAD_LEFT),
            'total_sin_iva' => 0,
            'iva' => 0,
            'total' => 0,
        ]);

        // Ahora los productos vienen manuales
        $items = json_decode($request->productos_json, true);

        if (!is_array($items) || empty($items)) {
            throw new \Exception('No se proporcionaron productos válidos.');
        }

        $subtotal = 0;

        foreach ($items as $item) {

            $descripcion = $item['descripcion'];
            $cantidad = $item['cantidad'];
            $precio_unitario = $item['precio'];
            $subtotal_detalle = $item['subtotal'];

            FacturaDetalle::create([
                'factura_id' => $factura->id,
                'descripcion' => $descripcion,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio_unitario,
                'subtotal' => $subtotal_detalle,
            ]);

            // Sumar al total general
            $subtotal += $subtotal_detalle;
        }

        // Cálculo de impuestos
        $iva = $subtotal * 0.13;
        $total = $subtotal + $iva;

        $factura->update([
            'total_sin_iva' => $subtotal,
            'iva' => $iva,
            'total' => $total,
        ]);
    });

    // --- Construcción de datos para la impresión ---
    $detalles = FacturaDetalle::where('factura_id', $factura->id)
        ->get()
        ->map(function ($detalle) {
            return (object)[
                'cantidad' => $detalle->cantidad,
                'descripcion' => $detalle->descripcion,
                'precio_unitario' => $detalle->precio_unitario,
                'preciouni' => $detalle->precio_unitario,
                'total' => $detalle->subtotal,
                'id' => $detalle->id,
                'coticode' => $detalle->factura_id,
            ];
        });

    $cliente = Cliente::where('id', $factura->cliente_id)->get();
    $actual = $factura->created_at;


    if ($request->tipo == "consumidor") {
        return view('contingencia.generardteconsumidor', compact('actual', 'detalles', 'cliente'));
    }elseif ($request->tipo == "ccf") {

        $codactividad = $cliente[0]->actividad_economica_id;

        $actividad = Actividad::where('codigo', $codactividad)->get();
        $actividad_descripcion = $actividad[0]->descripcion ?? 'No especificada';

         return view('contingencia.generardteccf', compact('actual', 'detalles', 'cliente', 'actividad_descripcion'));
    }
    }



}
