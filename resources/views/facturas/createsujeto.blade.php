@extends('layouts.app')

@section('content')

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="card">
    <div class="card-header">
        <h5>Factura Sujeto Excluido</h5>

        @if (session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif
    </div>

    <div class="card-body">
        <form action="{{ route('compras.storeSujetoExcluido') }}" method="POST">
            @csrf

            <!-- Proveedor -->
            <div class="mb-3">
                <label for="proveedor_id">Proveedor</label>
                <select name="proveedor_id" id="proveedor_id" class="form-control select2" required>
                    <option value="">Seleccione un proveedor</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <hr>
            <h5>Agregar Productos</h5>

            <!-- Línea para agregar productos -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Producto</label>
                    <select id="producto_id" class="form-control select2">
                        <option value="">Seleccione producto</option>
                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}"
                                data-nombre="{{ $producto->nombre }}"
                                data-precio="{{ $producto->precio_costo }}">
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Cantidad</label>
                    <input type="number" id="cantidad" class="form-control" value="1" min="1">
                </div>

                <div class="col-md-2">
                    <label>Precio</label>
                    <input type="number" id="precio" class="form-control" readonly>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="agregarProducto" class="btn btn-primary w-100">Agregar</button>
                </div>
            </div>

            <!-- Tabla productos -->
            <table class="table table-bordered" id="tablaProductos">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <input type="hidden" name="productos" id="productos_json">

            <div class="mt-4">
                <button class="btn btn-success">Guardar Factura</button>
                <a href="{{ route('compras.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script>
    let productos = [];

    $(document).ready(function () {
        $('.select2').select2();

        $('#producto_id').on('change', function () {
            let precio = $(this).find(':selected').data('precio') || 0;
            $('#precio').val(precio);
        });

        $('#agregarProducto').on('click', function () {
            let productoId = $('#producto_id').val();
            let nombre = $('#producto_id').find(':selected').data('nombre');
            let precio = parseFloat($('#precio').val());
            let cantidad = parseInt($('#cantidad').val());

            if (!productoId || cantidad <= 0 || isNaN(precio)) {
                alert("Seleccione producto, cantidad y precio válidos.");
                return;
            }

            let subtotal = (precio * cantidad).toFixed(2);

            productos.push({
                producto_id: productoId,
                descripcion: nombre,
                cantidad: cantidad,
                precio: precio
            });

            $('#tablaProductos tbody').append(`
                <tr>
                    <td>${nombre}</td>
                    <td>${cantidad}</td>
                    <td>$${precio.toFixed(2)}</td>
                    <td>$${subtotal}</td>
                    <td><button type="button" class="btn btn-danger btn-sm eliminarProducto" data-index="${productos.length - 1}">Eliminar</button></td>
                </tr>
            `);

            $('#producto_id').val(null).trigger('change');
            $('#cantidad').val(1);
            $('#precio').val('');

            actualizarJSON();
        });

        $('#tablaProductos').on('click', '.eliminarProducto', function () {
            let index = $(this).data('index');
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
