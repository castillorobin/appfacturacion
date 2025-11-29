<?php

namespace App\Http\Controllers;

use App\Models\Models\Cliente;
use Illuminate\Http\Request;

use App\Models\Actividad;
use App\Models\Departamento;
use App\Models\Municipio;

class ClienteWebController extends Controller
{
    public function index()
    {
        $clientes = Cliente::paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create', [
        'actividades' => Actividad::orderBy('descripcion')->get(),
        'departamentos' => Departamento::orderBy('valor')->get(),
        'municipios' => Municipio::orderBy('valor')->get(),
    ]);
      //  return view('clientes.create');
    }

    public function store(Request $request)
{

    $request->validate([
        'nombre' => 'nullable|string|max:255',
        'nombre_comercial' => 'nullable|string|max:255',
        'nit' => 'nullable|string|max:20',
        'dui' => 'nullable|string|max:20',
        'nrc' => 'nullable|string|max:20',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'correo' => 'nullable|email|max:255',
        'actividad_economica_id' => 'nullable|string|max:255',
        'departamento_id' => 'nullable|string|max:255',
        'municipio_id' => 'nullable|string|max:255',
    ]);

    

    Cliente::create($request->all());

    return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente');
}

   public function edit(Cliente $cliente)
{
    $actividades = Actividad::orderBy('descripcion')->get();
    $departamentos = Departamento::orderBy('valor')->get();
    $municipios = Municipio::orderBy('valor')->get();

    return view('clientes.edit', compact('cliente', 'actividades', 'departamentos', 'municipios'));
}

   public function update(Request $request, Cliente $cliente)
{
    $request->validate([
        'nombre' => 'nullable|string|max:255',
        'nombre_comercial' => 'nullable|string|max:255',
        'nit' => 'nullable|string|max:20',
        'dui' => 'nullable|string|max:20',
        'nrc' => 'nullable|string|max:20',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'correo' => 'nullable|email|max:255',
        'actividad_economica_id' => 'nullable|exists:actividades,id',
        'departamento_id' => 'nullable|exists:departamentos,id',
        'municipio_id' => 'nullable|exists:municipios,id',
    ]);

    $cliente->update($request->all());

    return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente');
}

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }

    public function show(Cliente $cliente)
{
    return view('clientes.show', compact('cliente'));
}
}