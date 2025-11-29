@extends('layouts.app')

@section('title', 'Cajas Registradas')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Cajas</h3>
        <a href="{{ route('cajas.create') }}" class="btn btn-success">Abrir Caja</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Fecha Apertura</th>
                <th>Fecha Cierre</th>
                <th>Monto Inicial</th>
                <th>Monto Final</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cajas as $caja)
            <tr>
                <td>{{ $caja->user->name }}</td>
                <td>{{ $caja->fecha_apertura }}</td>
                <td>{{ $caja->fecha_cierre ?? '-' }}</td>
                <td>${{ number_format($caja->monto_inicial, 2) }}</td>
                <td>{{ $caja->monto_final ? '$' . number_format($caja->monto_final, 2) : '-' }}</td>
                <td><span class="badge bg-{{ $caja->estado == 'abierta' ? 'success' : 'secondary' }}">{{ ucfirst($caja->estado) }}</span></td>
                <td>
    <a href="{{ route('cajas.movimientos', $caja) }}" class="btn btn-sm btn-info">
        Ver movimientos
    </a>
    @if(is_null($caja->fecha_cierre))
    <form action="{{ route('cajas.cerrar', $caja->id) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm">Cerrar Caja</button>
    </form>
@else
    <span class="badge bg-success">Cerrada</span>
@endif
</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No hay cajas registradas</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $cajas->links() }}
</div>
@endsection