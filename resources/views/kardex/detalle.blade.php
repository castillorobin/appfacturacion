@extends('layouts.app')

@section('title', 'Detalle Kardex')

@section('content')
<div class="container">
    <h3 class="mb-4">Kardex - Detalle del Producto</h3>

    <div class="mb-3">
        <strong>Producto:</strong> {{ $producto->nombre }} <br>
        <strong>Stock Actual:</strong> {{ $producto->stock }}
    </div>

    <table class="table table-striped table-bordered">
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
                
            </tr>
        </thead>
        <tbody>
            @forelse ($movimientos as $mov)
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
                    
                    
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">No hay movimientos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('kardex.index') }}" class="btn btn-secondary mt-3">← Volver al Kardex</a>
</div>
@endsection
