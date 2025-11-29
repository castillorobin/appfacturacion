<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor;
use Illuminate\Support\Str;
use App\Models\Kardex;

class CompraController extends Controller
{
        public function index()
    {
        $compras = \App\Models\Compra::with('detalles.producto')->orderBy('fecha', 'desc')->get();
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
       $productos = Producto::orderBy('nombre')->get();
    $proveedores = Proveedor::orderBy('nombre')->get();

    return view('compras.create', compact('productos', 'proveedores'));
    }

    public function store(Request $request)
{
    if (!obtenerCajaAbiertaUsuario()) {
        return back()->with('error', 'Debe abrir caja antes de realizar esta operación.');
    }

    $caja = obtenerCajaAbiertaUsuario();
    $compra = null; // <--- declarar aquí para usarla después
    $total = 0;

    $request->validate([
        'fecha' => 'required|date',
        'proveedor_id' => 'nullable|exists:proveedores,id',
        'productos' => 'required|array|min:1',
        'productos.*.producto_id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|integer|min:1',
        'productos.*.precio' => 'required|numeric|min:0',
    ]);

    DB::transaction(function () use ($request, &$compra, &$total) {
        $compra = Compra::create([
            'fecha' => $request->fecha,
            'proveedor_id' => $request->proveedor_id,
            'total' => 0,
        ]);

        foreach ($request->productos as $item) {
    $producto = Producto::findOrFail($item['producto_id']);
    $cantidad = $item['cantidad'];
    $precio = $item['precio'];
    $subtotal = $cantidad * $precio;

    CompraDetalle::create([
        'compra_id' => $compra->id,
        'producto_id' => $producto->id,
        'cantidad' => $cantidad,
        'precio_unitario' => $precio,
        'subtotal' => $subtotal,
    ]);

    // Valores anteriores
    $stock_anterior = $producto->stock ?? 0;
    $precio_anterior = $producto->precio_costo ?? 0;

    // Stock nuevo
    $nuevo_stock = $stock_anterior + $cantidad;

    // Cálculo del costo promedio ponderado
    $nuevo_precio_costo = $nuevo_stock > 0
        ? (($stock_anterior * $precio_anterior) + ($cantidad * $precio)) / $nuevo_stock
        : $precio;
 
    // Actualizar producto
    $producto->update([
        'stock' => $nuevo_stock,
        'precio_costo' => $nuevo_precio_costo,
    ]);

    // Crear movimiento en Kardex con valores correctos
    Kardex::create([
        'producto_id' => $producto->id,
        'fecha' => now(),
        'tipo' => 'CCF0',
        'documento' => $compra->id,
        'descripcion' => 'Compra registrada en ' . $compra->id,
        'Eunidad' => $cantidad,
        'Ecosto' => $precio,
        'Tunidad' => $nuevo_stock,
        'Tcostop' => $nuevo_precio_costo,
        'saldo' => $nuevo_stock * $nuevo_precio_costo,
    ]);

    $total += $subtotal;
}

        $compra->update(['total' => $total]);
    });

    // ✅ Aquí sí se cumple porque $compra ya está definido correctamente
    if ($caja && $compra) {
        MovimientoCaja::create([
            'caja_id' => $caja->id,
            'tipo' => 'egreso',
            'monto' => $total,
            'descripcion' => 'Compra registrada - ID: ' . $compra->id,
            'fecha' => now(),
            'referencia_id' => $compra->id,
            'referencia_type' => \App\Models\Compra::class,
            'user_id' => auth()->id(),
        ]);
    }

    return redirect()->route('compras.index')->with('success', 'Compra registrada correctamente.');
}


public function storeSujetoExcluido(Request $request)
{
     if (!obtenerCajaAbiertaUsuario()) {
        return back()->with('error', 'Debe abrir caja antes de realizar esta operación.');
    }

    $caja = obtenerCajaAbiertaUsuario();
    $compra = null; // <--- declarar aquí para usarla después
    $total = 0;

    // ✅ Decodificar el JSON de productos
    $request->merge([
        'productos' => json_decode($request->input('productos'), true)
    ]);

    $request->validate([
        'proveedor_id' => 'required|exists:proveedores,id',
        'productos' => 'required|array|min:1',
        'productos.*.producto_id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|numeric|min:1',
        'productos.*.precio' => 'required|numeric|min:0.01',
    ]);

    //  Registrar compra
        $compra = new Compra();
        $compra->proveedor_id = $request->proveedor_id;
       // $compra->tipo_factura = 'sujeto_excluido';
        $compra->total = $total;
       // $compra->codigo_generacion = strtoupper(Str::uuid());
        ///$compra->numero_control = 'DTE-06-M001P001-' . str_pad(mt_rand(1, 999999999999999), 15, '0', STR_PAD_LEFT);
        $compra->fecha = now();
        $compra->save();
   
    try {
        DB::beginTransaction();

        $total = 0;
        $detalles = [];

        foreach ($request->productos as $item) {
            $producto = Producto::find($item['producto_id']);

            //  Aumentar stock como una compra
            $producto->stock += $item['cantidad'];
            $producto->save();

            $subtotal = round($item['precio'] * $item['cantidad'], 2);
            $total += $subtotal;

            $detalles[] = new CompraDetalle([
                'producto_id' => $producto->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
                'subtotal' => $subtotal,
            ]);

                     // Registrar en Kardex
                     dd($item);
    Kardex::create([
        'producto_id' => $producto->id,
        'fecha' => now(),
        'tipo' => 'CCF0',
        'documento' => $compra->id,
        'descripcion' => $producto->nombre,
        'Eunidad' => $item['cantidad'],
        'Ecosto' => $item['precio'],
        'Tunidad' => $item['cantidad'] + $producto->stock,
        'Tcostop' => $producto->precio_costo,
        'saldo' => ($item['cantidad'] + $producto->stock) * $producto->precio_costo,
    ]);
        }

        

        // Detalles de productos
        $compra->detalles()->saveMany($detalles);
if ($caja && $compra) {
        MovimientoCaja::create([
            'caja_id' => $caja->id,
            'tipo' => 'egreso',
            'monto' => $total,
            'descripcion' => 'Compra registrada - ID: ' . $compra->id,
            'fecha' => now(),
            'referencia_id' => $compra->id,
            'referencia_type' => \App\Models\Compra::class,
            'user_id' => auth()->id(),
        ]);
    }
       

        DB::commit();

        $actual = $compra->created_at;
        $cliente = Proveedor::findOrFail($request->proveedor_id);
//dd($cliente);
 $detalles = CompraDetalle::with('producto')
    ->where('compra_id', $compra->id)
    ->get()
    ->map(function ($detalle) {
        $total = $detalle->cantidad * $detalle->precio_unitario;
        return (object)[
            'cantidad' => $detalle->cantidad,
            'descripcion' => $detalle->producto->nombre,
            'precio_unitario' => $detalle->precio_unitario,
            'preciouni' => $detalle->precio_unitario,
            'total' => $total,
            'id' => $detalle->id,
            'coticode' => $detalle->compra_id,
        ];
    });



        return view('facturas.generardtesujetoexcluido', compact('actual', 'detalles', 'cliente'));

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Error al registrar: ' . $e->getMessage());
    }
}
}