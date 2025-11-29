@extends('layouts.app')


@section('title', 'Ajuste de Inventario')


@section('content')
<div class="card">
<div class="card-header">
<h5>Ajuste de Inventario</h5>
</div>
<div class="card-body">
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
<form method="POST" action="{{ route('ajustes.store') }}">
@csrf
<div class="mb-3">
<label>Producto</label>
<select name="producto_id" class="form-select" required>
<option value="">-- Seleccione --</option>
@foreach($productos as $producto)
<option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
@endforeach
</select>
</div>
<div class="mb-3">
<label>Tipo de ajuste</label>
<select name="tipo" class="form-select" required>
<option value="entrada">Entrada</option>
<option value="salida">Salida</option>
</select>
</div>
<div class="mb-3">
<label>Cantidad</label>
<input type="number" name="cantidad" class="form-control" min="1" required>
</div>
<div class="mb-3">
<label>Motivo (opcional)</label>
<input type="text" name="motivo" class="form-control">
</div>
<button class="btn btn-success">Guardar ajuste</button>
</form>
</div>
</div>
@endsection