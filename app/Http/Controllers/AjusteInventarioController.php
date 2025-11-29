<?php

namespace App\Http\Controllers;


use App\Models\AjusteInventario;
use App\Models\Producto;
use Illuminate\Http\Request;


class AjusteInventarioController extends Controller
{
public function create()
{
$productos = Producto::all();
return view('ajustes.create', compact('productos'));
}


public function store(Request $request)
{
$request->validate([
'producto_id' => 'required|exists:productos,id',
'tipo' => 'required|in:entrada,salida',
'cantidad' => 'required|integer|min:1',
]);


$ajuste = AjusteInventario::create($request->all());


// Actualizar stock
$producto = $ajuste->producto;
if ($ajuste->tipo === 'entrada') {
$producto->stock += $ajuste->cantidad;
} else {
$producto->stock -= $ajuste->cantidad;
}
$producto->save();


return redirect()->back()->with('success', 'Ajuste registrado.');
}
}