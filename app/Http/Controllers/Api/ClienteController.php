<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Models\Cliente;

class ClienteController extends Controller
{
    public function index()
    {
        return Cliente::all();
    }

    public function store(StoreClienteRequest $request)
    {
        $cliente = Cliente::create($request->validated());
        return response()->json($cliente, 201);
    }

    public function show($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['mensaje' => 'Cliente no encontrado'], 404);
        }
        return $cliente;
    }

    public function update(UpdateClienteRequest $request, $id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['mensaje' => 'Cliente no encontrado'], 404);
        }
        $cliente->update($request->validated());
        return response()->json($cliente);
    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['mensaje' => 'Cliente no encontrado'], 404);
        }
        $cliente->delete();
        return response()->json(['mensaje' => 'Cliente eliminado correctamente']);
    }
}