@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="container">
    <h2>Editar Cliente</h2>

    <form action="{{ route('clientes.update', $cliente) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $cliente->nombre) }}">
        </div>

        <div class="mb-3">
            <label>Nombre Comercial</label>
            <input type="text" name="nombre_comercial" class="form-control" value="{{ old('nombre_comercial', $cliente->nombre_comercial) }}">
        </div>
 
        <div class="mb-3">
            <label>NIT</label>
            <input type="text" name="nit" class="form-control" value="{{ old('nit', $cliente->nit) }}">
        </div>

        <div class="mb-3">
            <label>DUI</label>
            <input type="text" name="dui" class="form-control" value="{{ old('dui', $cliente->dui) }}">
        </div>

        <div class="mb-3">
            <label>NRC</label>
            <input type="text" name="nrc" class="form-control" value="{{ old('nrc', $cliente->nrc) }}">
        </div>

        <div class="mb-3">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}">
        </div>

        <div class="mb-3">
            <label>Correo</label>
            <input type="email" name="correo" class="form-control" value="{{ old('correo', $cliente->correo) }}">
        </div>

        <div class="mb-3">
            <label>Actividad Económica</label>
            <select name="actividad_economica_id" class="form-control select2 w-100">
                <option value="{{ $cliente->actividad_economica_id }}">{{ $cliente->actividad_economica_id }}</option>
                @foreach ($actividades as $actividad)
                    <option value="{{ $actividad->codigo }}" >
                        {{ $actividad->descripcion }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Departamento</label>
            <select name="departamento_id" class="form-control select2 w-100">
                <option value="{{ $cliente->departamento_id }}">{{ $cliente->departamento_id }}</option>
                @foreach ($departamentos as $dpto)
                    <option value="{{ $dpto->codigo }}" >
                        {{ $dpto->valor }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Municipio</label>
            <select name="municipio_id" class="form-control select2 w-100">
                <option value="{{ $cliente->municipio_id }}">{{ $cliente->municipio_id }}</option>
                @foreach ($municipios as $mun)
                    <option value="{{ $mun->id }}" >
                        {{ $mun->valor }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: 'Seleccione',
            allowClear: true
        });
    });
</script>
@endsection