@extends('layouts.guest')

@section('title', 'Registrarse')

@section('content')
    <h4 class="mb-4 text-center">Registrarse</h4>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" required autofocus>
            @error('name')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" class="form-control" required>
            @error('email')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" required>
            @error('password')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Crear cuenta</button>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </form>
@endsection