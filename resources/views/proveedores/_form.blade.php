<div class="row">
    <div class="col-md-6 mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $proveedor->nombre ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Dirección</label>
        <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $proveedor->direccion ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Tipo de Persona</label>
        <select name="tipo_persona" class="form-control" required>
            <option value="natural" {{ old('tipo_persona', $proveedor->tipo_persona ?? '') == 'natural' ? 'selected' : '' }}>Natural</option>
            <option value="juridica" {{ old('tipo_persona', $proveedor->tipo_persona ?? '') == 'juridica' ? 'selected' : '' }}>Jurídica</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label>Tipo de Contribuyente</label>
        <select name="tipo_contribuyente" class="form-control" required>
            <option value="pequeño" {{ old('tipo_contribuyente', $proveedor->tipo_contribuyente ?? '') == 'pequeño' ? 'selected' : '' }}>Pequeño</option>
            <option value="mediano" {{ old('tipo_contribuyente', $proveedor->tipo_contribuyente ?? '') == 'mediano' ? 'selected' : '' }}>Mediano</option>
            <option value="grande" {{ old('tipo_contribuyente', $proveedor->tipo_contribuyente ?? '') == 'grande' ? 'selected' : '' }}>Grande</option>
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>País</label>
        <input type="text" name="pais" class="form-control" value="{{ old('pais', $proveedor->pais ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>Departamento</label>
        <input type="text" name="departamento" class="form-control" value="{{ old('departamento', $proveedor->departamento ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>Municipio</label>
        <input type="text" name="municipio" class="form-control" value="{{ old('municipio', $proveedor->municipio ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $proveedor->telefono ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label>Giro</label>
        <input type="text" name="giro" class="form-control" value="{{ old('giro', $proveedor->giro ?? '') }}">
    </div>

    <div class="col-md-2 mb-3">
        <label>DUI</label>
        <input type="text" name="dui" class="form-control" value="{{ old('dui', $proveedor->dui ?? '') }}">
    </div>

    <div class="col-md-2 mb-3">
        <label>NIT</label>
        <input type="text" name="nit" class="form-control" value="{{ old('nit', $proveedor->nit ?? '') }}" required>
    </div>
</div>