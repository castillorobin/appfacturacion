<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Kardex;
use App\Models\Producto;


class KardexController extends Controller
{

public function index(Request $request)
{
    $query = Kardex::with('producto')->orderByDesc('fecha');

    if ($request->filled('producto_id')) {
        $query->where('producto_id', $request->producto_id);
    }

    if ($request->filled('desde')) {
        $query->whereDate('fecha', '>=', $request->desde);
    }

    if ($request->filled('hasta')) {
        $query->whereDate('fecha', '<=', $request->hasta);
    }

    $kardex = $query->paginate(20);
    
    $productos = \App\Models\Producto::all();

    return view('kardex.index', compact('kardex', 'productos'));
}


public function show(Producto $producto)
{
$movimientos = Kardex::where('producto_id', $producto->id)->orderBy('fecha')->get();
return view('kardex.detalle', compact('producto', 'movimientos'));
}
}