<?php

namespace App\Http\Controllers;


use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Models\Cliente;
use App\Models\Producto;
use App\Models\MovimientoCaja;
use App\Models\Proveedor;
use App\Models\Kardex;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DTEService;
use App\Models\Actividad;
use App\Models\ConteoDTE;


class FacturaController extends Controller
{
public function index()
{
$facturas = Factura::latest()->paginate(10);
return view('facturas.index', compact('facturas'));
}


public function create()
{
$clientes = Cliente::all();
$productos = Producto::all();
return view('facturas.create', compact('clientes', 'productos'));
}
public function createSujetoExcluido()
{
    
$proveedores = Proveedor::all();

return view('facturas.create_sujeto_excluido', compact('proveedores'));
}

public function crearsujeto()
{
    $proveedores = Proveedor::all();
    return view('facturas.createsujeto', compact('proveedores'));
}

public function generarSujetoExcluido()
{
     if (!obtenerCajaAbiertaUsuario()) {
        return back()->with('error', 'Debe abrir caja antes de realizar esta operación.');
    }

    $caja = obtenerCajaAbiertaUsuario();
    $factura = null; // <-- declarar aquí

    $request->validate([
        'fecha' => 'required|date',
        'proveedor_id' => 'nullable|exists:proveedores,id',
        'productos' => 'required|array|min:1',
        'productos.*.descripcion' => 'required|string|min:3|max:255',
        'productos.*.cantidad' => 'required|integer|min:1',
        'productos.*.precio' => 'required|numeric|min:0',
    ]);

    


}


public function store(Request $request)
{
    $factura = null;

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
        $conteo = ConteoDTE::where('tipo', '01')->first();
        return view('facturas.generardteconsumidor', compact('actual', 'detalles', 'cliente', 'conteo'));
    } else if ($request->tipo == "ccf") {

        $codactividad = $cliente[0]->actividad_economica_id;
        $actividad = Actividad::where('codigo', $codactividad)->get();
        $actividad_descripcion = $actividad[0]->descripcion ?? 'No especificada';
        $conteo = ConteoDTE::where('tipo', '03')->first();

        return view('facturas.generardteccf', compact('actual', 'detalles', 'cliente', 'actividad_descripcion', 'conteo'));
    }
}

public function show(Factura $factura)
{
    // Cargar detalles relacionados si no están con lazy loading
    $factura->load('cliente', 'detalles.producto');

    return view('facturas.show', compact('factura'));
}

public function destroy(Factura $factura)
{
    DB::transaction(function () use ($factura) {
        // Cargar los detalles con sus productos
        $factura->load('detalles.producto');

        // Restaurar el stock de cada producto
        foreach ($factura->detalles as $detalle) {
            $producto = $detalle->producto;
            $producto->stock += $detalle->cantidad;
            $producto->save();
        }

        // Eliminar los detalles (si tienes restricción en DB puede que se eliminen en cascada)
        $factura->detalles()->delete();

        // Eliminar la factura
        $factura->delete();
    });

    return redirect()->route('facturas.index')->with('success', 'Factura eliminada y stock restaurado.');
}

}