<div class="mb-3">
<label class="form-label">Nombre</label>
<input type="text" name="nombre" class="form-control" value="{{ old('nombre', $categoria->nombre ?? '') }}" required>
</div>
<div class="mb-3">
<label class="form-label">Descripción</label>
<textarea name="descripcion" class="form-control">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
</div>