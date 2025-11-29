@extends('layouts.app')

@section('title', 'Historial de Compras')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Historial de Compras</h2>
        <a href="{{ route('compras.create') }}" class="btn btn-primary">Registrar Nueva Compra</a>
    </div>

    @if($compras->count())
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Total ($)</th>
                    <th>Productos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compras as $compra)
                    <tr>
                        <td>{{ $compra->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}</td>
                        <td>{{ number_format($compra->total, 2) }}</td>
                        <td>
                            <ul class="list-unstyled mb-0">
                                @foreach($compra->detalles as $detalle)
                                    <li>{{ $detalle->producto->nombre }} - {{ $detalle->cantidad }} x ${{ number_format($detalle->precio_unitario, 2) }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No hay compras registradas.</div>
    @endif
</div>
@endsection