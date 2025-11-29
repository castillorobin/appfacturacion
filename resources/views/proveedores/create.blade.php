@extends('layouts.app')

@section('title', 'Registrar Proveedor')

@section('content')
<div class="container">
    <h2>Registrar Proveedor</h2>

    <form action="{{ route('proveedores.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Dirección</label>
                <input type="text" name="direccion" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label>Tipo de Persona</label>
                <select name="tipo_persona" class="form-control" required>
                    <option value="natural">Natural</option>
                    <option value="juridica">Jurídica</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label>Tipo de Contribuyente</label>
                <select name="tipo_contribuyente" class="form-control" required>
                    <option value="pequeño">Pequeño</option>
                    <option value="mediano">Mediano</option>
                    <option value="grande">Grande</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>País</label>
                <input type="text" name="pais" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>Departamento</label>
                <input type="text" name="departamento" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>Municipio</label>
                <input type="text" name="municipio" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>Teléfono</label>
                <input type="text" name="telefono" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Giro</label>
                <input type="text" name="giro" class="form-control">
            </div>

            <div class="col-md-2 mb-3">
                <label>DUI</label>
                <input type="text" name="dui" class="form-control">
            </div>

            <div class="col-md-2 mb-3">
                <label>NIT</label>
                <input type="text" name="nit" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection