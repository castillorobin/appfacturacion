@extends('layouts.app')
@section('content')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (necesario para Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .select2-container .select2-selection--single {
        height: calc(2.375rem + 2px); /* Igual a .form-control en Bootstrap 4 */
        padding: 0.375rem 0.75rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        top: 0;
        right: 0.75rem;
    }
</style>

<div class="card">
<div class="card-header">
<h5>Emitir Factura</h5>
</div>
<div class="card-body">
<form action="{{ route('facturas.store') }}" method="POST">
@csrf
<div class="row mb-3">
<div class="col-md-4">
<div class="form-group">
    <label for="cliente_id">Cliente</label>
    <select id="cliente_id" name="cliente_id" class="form-control select2">
        <option value="">Seleccione un cliente</option>
        @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
        @endforeach
    </select>
</div>
</div>

<div class="col-md-3">
    <div class="form-group">
    <label for="cliente_id">Tipo</label>
    <select id="cliente_id" name="tipo" class="form-control" required>
       <option value="consumidor">Consumidor Final</option>
<option value="ccf">Crédito Fiscal</option>
    </select>
</div>

</div>

</div>

<hr>
<h6>Detalle de Productos</h6>
<div id="productos-wrapper">
<div class="row mb-2 producto-item">
<div class="col-md-4">

<div class="form-group">
    <label for="producto_id">Producto</label>
    <select id="producto_id" name="productos[0][producto_id]" class="form-control select2">
        <option value="">Seleccione un producto</option>
       @foreach($productos as $prod)
<option value="{{ $prod->id }}">{{ $prod->nombre }}</option>
@endforeach
    </select>
</div>

</div>
<div class="col-md-3">

<label for="cantidad">Cantidad</label>
<input type="number" name="productos[0][cantidad]" class="form-control" placeholder="Cantidad" min="1" required>
</div>
<div class="col-md-3">

<button type="button" class="btn btn-danger remove-item" style="margin-top: 30px;">X</button>
</div>
</div>
</div>
<button type="button" id="add-producto" class="btn btn-sm btn-secondary mb-3">+ Agregar producto</button>


<div>
<button class="btn btn-success">Guardar Factura</button>
<a href="{{ route('facturas.index') }}" class="btn btn-secondary">Cancelar</a>
</div>
</form>
</div>
</div>


<script>
let index = 1;
document.getElementById('add-producto').addEventListener('click', function () {
const wrapper = document.getElementById('productos-wrapper');
const item = document.querySelector('.producto-item').cloneNode(true);


// Resetear inputs
item.querySelectorAll('input').forEach(input => input.value = '');
item.querySelectorAll('select').forEach(select => select.selectedIndex = 0);


// Renombrar los name[]
item.querySelectorAll('select, input').forEach(field => {
field.name = field.name.replace(/\[\d+\]/, `[${index}]`);
});


index++;
wrapper.appendChild(item);
});


document.addEventListener('click', function (e) {
if (e.target.classList.contains('remove-item')) {
const items = document.querySelectorAll('.producto-item');
if (items.length > 1) e.target.closest('.producto-item').remove();
}
});
</script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Seleccione una opción",
            allowClear: true
        });
    });
</script>
@endsection