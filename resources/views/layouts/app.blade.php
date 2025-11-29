<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel Administrativo')</title>

    <!-- AdminLTE + Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">

     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE (solo si estás usándolo) -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Cerrar sesión</button>
                </form>
            </div>
        </li>
    </ul>
</nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link text-center">
            <span class="brand-text font-weight-light">Mi Sistema</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
                    {{-- 1. Administrar Caja --}}

{{-- 2. Administrar DTE --}}
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>
            Administrar DTE
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="/dtes" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Listado de DTE</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/contingencia" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>DTE en contingencia</p>
            </a>
        </li>
    </ul>
</li>

{{-- 3. Productos --}}
<li class="nav-item">
    <a href="{{ route('productos.index') }}" class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-boxes"></i>
        <p>Productos</p>
    </a>
</li>




{{-- 5. Clientes --}}
<li class="nav-item">
    <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>Clientes</p>
    </a>
</li>
{{-- 5. Proveedores --}}
<li class="nav-item">
    <a href="{{ route('proveedores.index') }}" class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-truck"></i>
        <p>Proveedores</p>
    </a>
</li>

{{-- 6. Facturación --}}
<li class="nav-item has-treeview {{ request()->routeIs('facturas.*') ? 'menu-open' : '' }}">

    <a href=" {{ route('facturas.create') }}" class="nav-link">
        <i class="nav-icon fas fa-file-invoice-dollar"></i>
        <p>
            Facturación
            
        </p>
    </a>
    
</li>

{{-- 7. Configuración --}}
<li class="nav-item has-treeview {{ request()->routeIs('categorias.*') || request()->routeIs('ajustes.create') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('categorias.*') || request()->routeIs('ajustes.create') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cogs"></i>
        <p>
            Configuración
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Categorías</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('ajustes.create') }}" class="nav-link {{ request()->routeIs('ajustes.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Ajuste de Inventario</p>
            </a>
        </li>
    </ul>
</li>



                    <!-- Puedes agregar más módulos aquí -->
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Contenido -->
    <div class="content-wrapper p-3">
        <section class="content">
            @yield('content')
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer text-center">
        <strong>© {{ now()->year }} - Sistema SV</strong>
    </footer>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap JS + Popper desde CDN 
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

-->

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



</body>
</html>