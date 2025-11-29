@extends('layouts.app')


@section('title', 'Facturas')
@section('content')
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center">
<h5>Facturas emitidas</h5>
<a href="{{ route('facturas.create') }}" class="btn btn-sm btn-primary">Nueva Factura</a>
</div>
<div class="card-body">
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
<table class="table table-bordered table-striped">
<thead>
<tr>
<th>Número</th>
<th>Cliente</th>
<th>Tipo</th>
<th>Fecha</th>
<th>Total</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
@foreach($facturas as $factura)
<tr>
<td>{{ $factura->numero }}</td>
<td>{{ $factura->cliente->nombre }}</td>
<td>{{ ucfirst($factura->tipo) }}</td>
<td>{{ $factura->fecha }}</td>
<td>${{ number_format($factura->total, 2) }}</td>
 <td>
    <a href="{{ route('facturas.show', $factura) }}" class="btn btn-sm btn-info">Ver</a>

    <form action="{{ route('facturas.destroy', $factura) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta factura?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
    </form>
</td>
</tr>
@endforeach
</tbody>
</table>
{{ $facturas->links() }}
</div>
</div>
@endsection