@extends('layouts.app')

@section('title', 'Listado de Proveedores')

@section('content')
<div class="container">
    <h2>Proveedores</h2>

    <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('proveedores.create') }}" class="btn btn-success">
        Nuevo Proveedor
    </a>
</div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
    <tr>
        <th>Nombre</th>
        <th>Tipo Persona</th>
        <th>Tipo Contribuyente</th>
        <th>País</th>
        <th>Municipio</th>
        <th>Teléfono</th>
        <th>Acciones</th>
    </tr>
</thead>
<tbody>
    @forelse ($proveedores as $prov)
        <tr>
            <td>{{ $prov->nombre }}</td>
            <td>{{ ucfirst($prov->tipo_persona) }}</td>
            <td>{{ ucfirst($prov->tipo_contribuyente) }}</td>
            <td>{{ $prov->pais }}</td>
            <td>{{ $prov->municipio }}</td>
            <td>{{ $prov->telefono }}</td>
            <td>
                <a href="{{ route('proveedores.edit', $prov) }}" class="btn btn-sm btn-primary">Editar</a>

                <form action="{{ route('proveedores.destroy', $prov) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este proveedor?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="7">No hay proveedores registrados.</td></tr>
    @endforelse
</tbody>
    </table>

    {{ $proveedores->links() }}
</div>
@endsection