@extends('layouts.app')


@section('title', 'Crear Categoría')


@section('content')
<div class="card">
<div class="card-header"><h5>Nueva Categoría</h5></div>
<div class="card-body">
<form action="{{ route('categorias.store') }}" method="POST">
@csrf
@include('categorias.form')
<button class="btn btn-success">Guardar</button>
<a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
</div>
</div>
@endsection