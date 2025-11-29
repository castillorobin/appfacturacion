@extends('layouts.app')


@section('title', 'DTE en Contingencia')


@section('content')
<div class="container">
<h3 class="mb-4">DTE en Contingencia</h3>
<div class="d-flex justify-content-end mb-3" style="width:100%;">
<a href="{{ route('contingencia.crear') }}" class="btn btn-sm btn-primary">Crear Contingencia</a>
</div>
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif


@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif


<table class="table table-bordered table-hover">
<thead class="thead-light">
<tr>
<th>#</th>
<th>Número de Control</th>
<th>Código Generación</th>
<th>Tipo DTE</th>
<th>Fecha</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
@forelse ($dtes as $dte)
<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $dte->numero_control }}</td>
<td>{{ $dte->codigo_generacion }}</td>
<td>{{ $dte->tipo_dte }}</td>
<td>{{ $dte->created_at->format('d/m/Y H:i') }}</td>
<td>
    @if($dte->estado === 'pendiente')
<form action="{{ route('contingencia.reportar', $dte->id) }}" method="POST" class="d-inline">
@csrf
<button type="submit" class="btn btn-warning btn-sm">Reportar</button>
</form>
@endif
@if($dte->estado === 'reportado')
<form action="{{ route('contingencia.enviar', $dte->id) }}" method="POST" class="d-inline">
@csrf
<button type="submit" class="btn btn-primary btn-sm">Enviar</button>
</form>
@endif
@if($dte->estado === 'enviado')
<span class="badge badge-success">Transmitido</span>
@endif
</td>
</tr>
@empty
<tr>
<td colspan="6" class="text-center">No hay DTE generados en contingencia.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
@endsection