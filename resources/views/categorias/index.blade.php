@extends('layouts.app')


@section('title', 'Categorías')


@section('content')
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center">
<h5 class="mb-0">Categorías</h5>
<a href="{{ route('categorias.create') }}" class="btn btn-sm btn-primary">Nueva Categoría</a>
</div>
<div class="card-body">
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
<table class="table table-bordered">
<thead>
<tr>
<th>Nombre</th>
<th>Descripción</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
@foreach($categorias as $categoria)
<tr>
<td>{{ $categoria->nombre }}</td>
<td>{{ $categoria->descripcion }}</td>
<td>
<a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-warning">Editar</a>
<form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="d-inline">
@csrf
@method('DELETE')
<button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
</form>
</td>
</tr>
@endforeach
</tbody>
</table>
{{ $categorias->links() }}
</div>
</div>
@endsection