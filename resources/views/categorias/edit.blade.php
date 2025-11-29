@extends('layouts.app')


@section('title', 'Editar Categoría')


@section('content')
<div class="card">
<div class="card-header"><h5>Editar: {{ $categoria->nombre }}</h5></div>
<div class="card-body">
<form action="{{ route('categorias.update', $categoria) }}" method="POST">
@csrf
@method('PUT')
@include('categorias.form')
<button class="btn btn-primary">Actualizar</button>
<a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
</div>
</div>
@endsection