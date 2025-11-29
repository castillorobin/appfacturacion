<?php

namespace App\Http\Controllers;
use App\Models\Proveedor;

use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $proveedores = Proveedor::latest()->paginate(10);
    return view('proveedores.index', compact('proveedores'));
}

public function create()
{
    return view('proveedores.create');
}

public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'direccion' => 'nullable|string',
        'tipo_persona' => 'required|in:natural,juridica',
        'tipo_contribuyente' => 'required|in:pequeño,mediano,grande',
        'pais' => 'required|string',
        'departamento' => 'required|string',
        'municipio' => 'required|string',
        'telefono' => 'nullable|string|max:20',
        'giro' => 'nullable|string',
        'dui' => 'nullable|string|max:10',
        'nit' => 'required|string|max:17',
    ]);

    Proveedor::create($request->all());

    return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado correctamente.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    // Editar
public function edit(Proveedor $proveedor)
{
    return view('proveedores.edit', compact('proveedor'));
}

// Actualizar
public function update(Request $request, Proveedor $proveedor)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'direccion' => 'nullable|string',
        'tipo_persona' => 'required|in:natural,juridica',
        'tipo_contribuyente' => 'required|in:pequeño,mediano,grande',
        'pais' => 'required|string',
        'departamento' => 'required|string',
        'municipio' => 'required|string',
        'telefono' => 'nullable|string|max:20',
        'giro' => 'nullable|string',
        'dui' => 'nullable|string|max:10',
        'nit' => 'required|string|max:17',
    ]);

    $proveedor->update($request->all());

    return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
}

// Eliminar
public function destroy(Proveedor $proveedor)
{
    $proveedor->delete();

    return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente.');
}
   
}
