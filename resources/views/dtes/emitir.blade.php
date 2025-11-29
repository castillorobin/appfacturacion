@extends('layouts.app')

@section('title', 'Emitir Nota de Crédito')

@section('content')
<div class="container">
    <h3 class="mb-4">Emitir Nota de Crédito</h3>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>DTE Original</strong>
        </div>
        <div class="card-body">
            <p><strong>Número de Control:</strong> {{ $dte->numero_control }}</p>
            <p><strong>Código Generación:</strong> {{ $dte->codigo_generacion }}</p>
            <p><strong>Tipo de DTE:</strong> {{ $dte->tipo_dte == '01' ? 'Consumidor Final' : ($dte->tipo_dte == '03' ? 'Crédito Fiscal' : 'Otro') }}</p>
            <p><strong>Monto total:</strong> ${{ number_format($dte->getMontoTotal(), 2) }}</p>
        </div>
    </div>

    <form action="{{ route('notas-credito.emitirDesdeDTE', $dte->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="monto">Monto a devolver <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" max="{{ $dte->getMontoTotal() }}" name="monto" id="monto" class="form-control" placeholder="Ej: {{ $dte->getMontoTotal() }}" required>
            <small class="form-text text-muted">Puede ingresar un monto parcial o el total para emitir la nota de crédito.</small>
        </div>

        <div class="form-group mt-3">
            <label for="motivo">Motivo de la nota de crédito <span class="text-danger">*</span></label>
            <textarea name="motivo" id="motivo" class="form-control" rows="3" required>Devolución de producto</textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Emitir Nota de Crédito</button>
        <a href="{{ route('dtes.index') }}" class="btn btn-secondary mt-4">Cancelar</a>
    </form>
</div>
@endsection