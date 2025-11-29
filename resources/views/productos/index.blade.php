@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de Productos</h5>
        <a href="{{ route('productos.create') }}" class="btn btn-sm btn-primary">Agregar Producto</a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-sm table-striped">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Unidad</th>
                    <th>Precio Costo</th>
                    <th>Precio Venta</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                    <tr>
                        <td>{{ $producto->codigo }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->unidad }}</td>
                        <td>${{ number_format($producto->precio_costo, 2) }}</td>
                        <td>${{ number_format($producto->precio_venta, 2) }}</td>
                        <td>  {{ $producto->stock }}
    @if($producto->stock <= $producto->stock_minimo)
        <span class="badge bg-danger">Bajo</span>
    @endif</td>
                        <td>
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('¿Eliminar producto?')" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No hay productos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $productos->links() }}
    </div>
</div>
@endsection