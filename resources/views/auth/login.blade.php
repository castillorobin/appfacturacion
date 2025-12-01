@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@section('content')
    <h4 class="mb-4 text-center">Iniciar Sesión</h4>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" class="form-control" required autofocus>
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

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Recordarme</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>

        
    </form>
@endsection