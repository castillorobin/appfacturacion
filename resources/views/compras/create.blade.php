@extends('layouts.app')

@section('title', 'Registrar Compra')

@section('content')
<div class="container">
    <h2>Registrar Compra</h2>
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
    <form action="{{ route('compras.store') }}" method="POST">
        @csrf

        <div class="col-md-4 form-group mb-3">
            <label for="fecha">Fecha de compra</label>
            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <div class="col-md-4 form-group mb-3">
    <label for="proveedor_id">Proveedor</label>
    <select name="proveedor_id" id="proveedor_id" class="form-control select2">
        <option value="">Seleccione un proveedor</option>
        @foreach ($proveedores as $proveedor)
            <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
        @endforeach
    </select>
</div>

        <hr>

        <h5>Agregar Productos</h5>
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Producto</label>
                <select id="producto_id" class="form-control select2">
                    <option value="">Seleccione</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}"
                                data-nombre="{{ $producto->nombre }}"
                        >{{ $producto->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Cantidad</label>
                <input type="number" id="cantidad" class="form-control" min="1" value="1">
            </div>

            <div class="col-md-3">
                <label>Precio Unitario</label>
                <input type="number" id="precio" class="form-control" min="0" step="0.01" value="0.00">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100" id="agregar">Agregar</button>
            </div>
        </div>

        <table class="table table-bordered" id="tabla-productos">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="mb-3 text-end">
            <strong>Total: $<span id="total">0.00</span></strong>
        </div>

        <button type="submit" class="btn btn-primary">Registrar Compra</button>
    </form>
</div>



<script>
    let total = 0;
let index = 0; // 👈 para llevar control de los inputs


$('#agregar').click(function () {
    const prodId = $('#producto_id').val();
    const nombre = $('#producto_id option:selected').data('nombre');
    const cantidad = parseInt($('#cantidad').val());
    const precio = parseFloat($('#precio').val());
    const subtotal = cantidad * precio;

    if (!prodId || isNaN(cantidad) || cantidad <= 0 || isNaN(precio)) {
        alert('Complete correctamente los datos del producto');
        return;
    }

    const fila = `
    <tr>
        <td>
            ${nombre}
            <input type="hidden" name="productos[${index}][producto_id]" value="${prodId}">
        </td>
        <td>
            ${cantidad}
            <input type="hidden" name="productos[${index}][cantidad]" value="${cantidad}">
        </td>
        <td>
            ${precio.toFixed(2)}
            <input type="hidden" name="productos[${index}][precio]" value="${precio}">
        </td>
        <td>${subtotal.toFixed(2)}</td>
        <td><button type="button" class="btn btn-danger btn-sm eliminar">X</button></td>
    </tr>
`;

index++;

    $('#tabla-productos tbody').append(fila);
    total += subtotal;
    $('#total').text(total.toFixed(2));

    // Reset
    $('#producto_id').val('').trigger('change');
    $('#cantidad').val(1);
    $('#precio').val(0);
});

// Eliminar fila
$(document).on('click', '.eliminar', function () {
    const fila = $(this).closest('tr');
    const subtotal = parseFloat(fila.find('td').eq(3).text());
    total -= subtotal;
    $('#total').text(total.toFixed(2));
    fila.remove();
});
</script>
@endsection