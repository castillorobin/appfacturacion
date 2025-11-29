@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Agregar nuevo producto</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('productos.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Código</label>
                <input type="text" name="codigo" class="form-control" required>
                @error('codigo') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
                @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Unidad</label>
                <input type="text" name="unidad" class="form-control" placeholder="Ej: Unidad, Caja, Litro" required>
                @error('unidad') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
    <label class="form-label">Categoría</label>
    <select name="categoria_id" class="form-select">
        <option value="">-- Seleccione --</option>
        @foreach(\App\Models\Categoria::all() as $cat)
            <option value="{{ $cat->id }}"
                {{ old('categoria_id', $producto->categoria_id ?? '') == $cat->id ? 'selected' : '' }}>
                {{ $cat->nombre }}
            </option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Stock mínimo</label>
    <input type="number" name="stock_minimo" class="form-control"
           value="{{ old('stock_minimo', $producto->stock_minimo ?? 0) }}">
</div>

            <div class="mb-3">
                <label class="form-label">Precio Costo</label>
                <input type="number" step="0.01" name="precio_costo" class="form-control" required>
                @error('precio_costo') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Precio Venta</label>
                <input type="number" step="0.01" name="precio_venta" class="form-control" required>
                @error('precio_venta') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button class="btn btn-success">Guardar</button>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection