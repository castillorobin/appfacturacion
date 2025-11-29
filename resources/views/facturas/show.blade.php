@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Factura {{ $factura->numero }}</h2>
    <p><strong>Cliente:</strong> {{ $factura->cliente->nombre }}</p>
    <p><strong>Tipo:</strong> {{ $factura->tipo }}</p>
    <p><strong>Fecha:</strong> {{ $factura->fecha->format('d/m/Y') }}</p>

    <h4>Detalles:</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->detalles as $detalle)
            <tr>
                <td>{{ $detalle->producto->nombre }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                <td>${{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>IVA:</strong> ${{ number_format($factura->iva, 2) }}</p>
    <p><strong>Total:</strong> ${{ number_format($factura->total, 2) }}</p>

    <a href="{{ route('facturas.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection