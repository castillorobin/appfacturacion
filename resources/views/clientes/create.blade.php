@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@section('content')

<style>
/* Estilo para igualar altura del select2 al form-control */
.select2-container .select2-selection--single {
    height: 38px !important; /* igual que input.form-control */
    padding: 6px 12px !important;
    font-size: 1rem;
    line-height: 1.5;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 24px !important; /* alinear verticalmente el texto */
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
}
</style>
<div class="container">
    <h2>Registrar Cliente</h2>

    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label>Nombre Comercial</label>
            <input type="text" name="nombre_comercial" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label>NIT</label>
            <input type="text" name="nit" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label>DUI</label>
            <input type="text" name="dui" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label>NRC</label>
            <input type="text" name="nrc" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label>Correo</label>
            <input type="email" name="correo" class="form-control">
        </div>

        <div class="form-group mb-3">
    <label>Actividad Económica</label>
    <select name="actividad_economica_id" id="actividad_economica_id" class="form-control select2  w-100">
        <option value="">Seleccione</option>
        @foreach($actividades as $actividad)
            <option value="{{ $actividad->codigo }}">
                {{ $actividad->codigo }} - {{ $actividad->descripcion }}
            </option>
        @endforeach
    </select>
</div>

        <div class="form-group mb-3">
            <label>Departamento</label>
            <select name="departamento_id" class="form-control">
                <option value="">Seleccione</option>
                @foreach($departamentos as $dpto)
                    <option value="{{ $dpto->codigo }}">{{ $dpto->valor }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Municipio</label>
            <select name="municipio_id" class="form-control">
                <option value="">Seleccione</option>
                @foreach($municipios as $muni)
                    <option value="{{ $muni->codigo }}">{{ $muni->valor }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cliente</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Seleccione una opción',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection