@extends('layouts.app')

@section('title', 'Kardex - Historial de Movimientos')

@section('content')
<div class="container">
    <h3 class="mb-4">Kardex</h3>

    {{-- Filtros --}}
    <form action="{{ route('kardex.index') }}" method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>Producto</label>
            <select name="producto_id" class="form-select">
                <option value="">Todos</option>
                @foreach ($productos as $producto)
                    <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                        {{ $producto->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>Desde</label>
            <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
        </div>

        <div class="col-md-3">
            <label>Hasta</label>
            <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    {{-- Tabla --}}
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr style="text-align: center; border: 1px solid #000;">
                <th style="text-align: center; border: 1px solid #000;">#</th>
                <th style="text-align: center; border: 1px solid #000;">Fecha</th>
                <th style="text-align: center; border: 1px solid #000;"># Doc</th>
                <th style="text-align: center; border: 1px solid #000;">Tipo</th>
                <th style="text-align: center; border: 1px solid #000;">Descripción</th>
                <th colspan="2" style="text-align: center; border: 1px solid #000;">Entradas</th>
                <th colspan="2" style="text-align: center; border: 1px solid #000;">Salidas</th>
                <th colspan="3" style="text-align: center; border: 1px solid #000;">Saldo</th>
                <th colspan="3" style="text-align: center; border: 1px solid #000;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        
            @forelse ($kardex as $mov)
                <tr class="text-center">
                    <td>{{ $mov->id}}</td>
                    <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                    
                    <td>{{ $mov->documento }}</td>
                    <td>{{ $mov->tipo }}</td>
                    
                    <td>{{ $mov->descripcion }}</td>
                    <td>{{ $mov->Eunidad }}</td>
                    <td>{{ $mov->Ecosto }}</td>
                    <td>{{ $mov->Sunidad }}</td>
                    <td>{{ $mov->Scosto }}</td>
                    <td>{{ $mov->Tunidad }}</td>
                    <td>{{ $mov->Tcostop }}</td>
                    <td>{{ $mov->saldo }}</td>
                       
                    </td>
                    
                    <td>
                        <a href="{{ route('kardex.detalle', $mov->producto_id) }}" class="btn btn-sm btn-primary">
                            Ver Historial
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">No hay movimientos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
