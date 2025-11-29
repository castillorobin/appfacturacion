@extends('layouts.app')

@section('title', 'Movimientos de Caja')

@section('content')

<div class="container">
    <h3>Movimientos de Caja #{{ $caja->id }}</h3>
    <p><strong>Fecha de apertura:</strong> {{ $caja->fecha_apertura }}</p>
    <p><strong>Fecha de cierre:</strong> {{ $caja->fecha_cierre ?? 'Caja aún abierta' }}</p>

    <table class="table table-bordered mt-3">
        <thead>
    <tr>
        <th>Tipo</th>
        <th>Monto</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <th>Balance acumulado</th>
    </tr>
</thead>
        <tbody>

@php
    $balance = $caja->monto_inicial ?? 0;
@endphp

 <tr>
    <td><span class="badge bg-secondary">Inicial</span></td>
    <td>${{ number_format($caja->monto_inicial, 2) }}</td>
    <td>Apertura de caja</td>
    <td>{{ $caja->created_at->format('d/m/Y g:i A') }}</td>
    <td><strong>${{ number_format($balance, 2) }}</strong></td>
</tr>

@forelse ($caja->movimientos->sortBy('fecha') as $mov)
    @php
        $mov->tipo === 'ingreso'
            ? $balance += $mov->monto
            : $balance -= $mov->monto;
    @endphp
   
    <tr>
        <td>
            <span class="badge {{ $mov->tipo === 'ingreso' ? 'bg-success' : 'bg-danger' }}">
                {{ ucfirst($mov->tipo) }}
            </span>
        </td>
        <td>${{ number_format($mov->monto, 2) }}</td>
        <td>{{ $mov->descripcion }}</td>
        <td>{{ $mov->created_at->format('d/m/Y g:i A') }}</td>
        <td><strong>${{ number_format($balance, 2) }}</strong></td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center">Sin movimientos registrados.</td>
    </tr>
@endforelse
</tbody>
    </table>

    <a href="{{ route('cajas.index') }}" class="btn btn-secondary">Volver a listado</a>
</div>
@endsection