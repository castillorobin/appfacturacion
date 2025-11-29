@extends('layouts.app')

@section('title', 'Abrir Caja')

@section('content')
<div class="container">
    <h3>Abrir Caja</h3>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('cajas.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="monto_inicial">Monto Inicial</label>
            <input type="number" name="monto_inicial" id="monto_inicial" class="form-control" required step="0.01" min="0">
        </div>

        <button type="submit" class="btn btn-primary">Abrir Caja</button>
    </form>
</div>
@endsection