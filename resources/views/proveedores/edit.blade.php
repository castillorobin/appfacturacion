@extends('layouts.app')

@section('title', 'Editar Proveedor')

@section('content')
<div class="container">
    <h2>Editar Proveedor</h2>

    <form action="{{ route('proveedores.update', $proveedor) }}" method="POST">
        @csrf
        @method('PUT')

        @include('proveedores._form', ['proveedor' => $proveedor])

        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection