@extends('layouts.app')

@section('title', 'Administrar DTEs')

@section('content')
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<h3>Documentos Tributarios Electrónicos (DTE)</h3>
<form method="GET" class="mb-3">
<div class="row">
    <div class="col-md-3">
    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}" placeholder="Desde">
    </div>
    <div class="col-md-3">
    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}" placeholder="Hasta">
    </div>
    <div class="col-md-3">
    <button type="submit" class="btn btn-primary">Filtrar</button>
    
    </div>
</form>
<a class="btn btn-success btn-sm"
   href="{{ route('dtes.descargarJsonLote', array_merge(request()->only(['desde','hasta']), ['tipo'=>request('tipo','legible')])) }}">
  Descargar todos los JSON 
</a>

</div>



<table class="table table-bordered">
<thead>
<tr>
<th>Número Control</th>
<th>Código Generación</th>
<th>Fecha</th>
<th>Tipo</th>
<th>Estado</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
@foreach ($dtes as $dte)
<tr>
<td>{{ $dte->numero_control }}</td>
<td>{{ $dte->codigo_generacion }}</td>
<td>{{ $dte->created_at->format('d/m/Y H:i') }}</td>
<td>
    @if($dte->tipo_dte === '14')
        Sujeto Excluido
    @elseif($dte->tipo_dte === '01')
        Consumidor Final
    @elseif($dte->tipo_dte === '05')
        Nota de Crédito
    @elseif($dte->tipo_dte === '03')
        Credito Fiscal
    @endif  
  
</td>
<td>
    @if($dte->estado === 'anulado')
        <span class="badge bg-danger">Anulado</span>
    @else
        <span class="badge bg-success">Activo</span>
    @endif
</td>
<td>
    <a href="{{ route('dtes.verPdf', $dte->id) }}" target="_blank" class="btn btn-sm btn-info">Ver PDF</a>

    @if($dte->json_legible_path || $dte->json_firmado_path)
        <a href="{{ route('dtes.descargarJson', $dte->id) }}" class="btn btn-sm btn-success">Descargar JSON</a>
    @endif
@if($dte->estado !== 'anulado')
    <form action="{{ route('dtes.anular', $dte->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Estás seguro de anular este DTE?');">
    @csrf
    <input type="hidden" name="motivo" value="Anulación por solicitud del cliente"> <!-- Puedes hacer esto dinámico si quieres -->
    <button type="submit" class="btn btn-sm btn-danger">Anular</button>
</form>
    @endif


@if($dte->tipo_dte === '03')
    <a href="{{ route('notas-credito.formEmitir', $dte->id) }}" class="btn btn-sm btn-warning">Nota de Crédito</a>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>


<div class="d-flex justify-content-end">
    {{ $dtes->onEachSide(1)->links('pagination::bootstrap-4') }}
</div>


</div>
@endsection