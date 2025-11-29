@extends('layouts.app')
@section('content')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container .select2-selection--single {
        height: calc(2.375rem + 2px);
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

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
</div>

<div class="card-body">

<form action="{{ route('facturas.store') }}" method="POST">
@csrf

{{-- ================== DATOS DEL CLIENTE ================== --}}
<div class="row mb-3">
    <div class="col-md-4">
        <label for="cliente_id">Cliente</label>
        <select id="cliente_id" name="cliente_id" class="form-control select2" required>
            <option value="">Seleccione un cliente</option>
            @foreach($clientes as $cliente)
                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label for="tipo">Tipo</label>
        <select name="tipo" class="form-control" required>
            <option value="consumidor">Consumidor Final</option>
            <option value="ccf">Crédito Fiscal</option>
        </select>
    </div>
</div>

<hr>

{{-- ================== DETALLE DE PRODUCTOS ================== --}}
<h4>Detalle de Productos</h4>
<br>

<div id="productos-wrapper">
    <div class="row mb-3">

        <div class="col-md-4">
            <label>Descripción del Producto</label>
            <input type="text" id="descripcion" class="form-control" placeholder="Ingrese el producto">
        </div>

        <div class="col-md-2">
            <label>Cantidad</label>
            <input type="number" id="cantidad" class="form-control" value="1" min="1" value="1">
        </div>

        <div class="col-md-2">
            <label>Precio</label>
            <input type="number" id="precio" class="form-control" min="0" step="0.01">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="button" id="agregarProducto" class="btn btn-primary w-100">Agregar</button>
        </div>
    </div>
</div>

{{-- ================== TABLA ================== --}}
<div class="row mb-3">
    <div class="col-md-10">
        <table class="table table-bordered" id="tablaProductos">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas dinámicas -->
            </tbody>
        </table>
    </div>
</div>

<input type="hidden" name="productos_json" id="productos_json">

<div>
    <button class="btn btn-success">Generar Factura</button>
    <a href="{{ route('facturas.index') }}" class="btn btn-secondary">Cancelar</a>
</div>

</form>
</div>
</div>

{{-- ================== SCRIPTS ================== --}}
<script>
    let productos = [];

    $(document).ready(function () {
        $('.select2').select2();

        // Agregar producto manual
        $('#agregarProducto').on('click', function () {
            const descripcion = $('#descripcion').val().trim();
            const cantidad = parseInt($('#cantidad').val());
            const precio = parseFloat($('#precio').val());

            if (!descripcion || cantidad <= 0 || precio <= 0) {
                alert("Debe ingresar descripción, cantidad y precio válidos.");
                return;
            }

            const subtotal = (precio * cantidad).toFixed(2);

            // Agregar al arreglo
            productos.push({
                descripcion,
                cantidad,
                precio,
                subtotal
            });

            // Agregar fila visual
            $('#tablaProductos tbody').append(`
                <tr>
                    <td>${cantidad}</td>
                    <td>${descripcion}</td>
                    <td>$${precio.toFixed(2)}</td>
                    <td>$${subtotal}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm eliminarProducto" data-index="${productos.length - 1}">Eliminar</button>
                    </td>
                </tr>
            `);

            // Limpiar inputs
            $('#descripcion').val('');
            $('#cantidad').val(1);
            $('#precio').val('');

            actualizarJSON();
        });

        // Eliminar producto de la tabla
        $('#tablaProductos').on('click', '.eliminarProducto', function () {
            const index = $(this).data('index');
            productos.splice(index, 1);
            $(this).closest('tr').remove();
            actualizarJSON();
        });

        function actualizarJSON() {
            $('#productos_json').val(JSON.stringify(productos));
        }
    });
</script>

@endsection
