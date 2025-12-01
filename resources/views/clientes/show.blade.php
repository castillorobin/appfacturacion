@extends('layouts.app')

@section('title', 'Detalle del Cliente')

@section('content')
<div class="container">
    <h2>Detalle del Cliente</h2>

    <div class="card">
        <div class="card-body">

            <p><strong>Nombre:</strong> {{ $cliente->nombre ?? '-' }}</p>
            <p><strong>Nombre Comercial:</strong> {{ $cliente->nombre_comercial ?? '-' }}</p>
            <p><strong>NIT:</strong> {{ $cliente->nit ?? '-' }}</p>
            <p><strong>DUI:</strong> {{ $cliente->dui ?? '-' }}</p>
            <p><strong>NRC:</strong> {{ $cliente->nrc ?? '-' }}</p>
            <p><strong>Teléfono:</strong> {{ $cliente->telefono ?? '-' }}</p>
            <p><strong>Correo:</strong> {{ $cliente->correo ?? '-' }}</p>

           

        </div>
    </div>

    <a href="{{ route('clientes.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection