<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Usuario (Bootstrap dropdown funcional) -->
<ul class="navbar-nav ms-auto">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle"
           href="#"
           id="userDropdown"
           role="button"
           data-bs-toggle="dropdown"
           aria-expanded="false">
            {{ Auth::user()->name }}
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Cerrar sesión</button>
                </form>
            </li>
        </ul>
    </li>
</ul>
</nav>