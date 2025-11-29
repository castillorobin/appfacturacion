@php
    // Espera recibir $dte como ARRAY (versión legible que construimos)
    $emisor   = $dte['emisor']   ?? [];
    $receptor = $dte['receptor'] ?? [];
    $ident    = $dte['identificacion'] ?? [];
    $resumen  = $dte['resumen'] ?? [];
    $items    = $dte['cuerpoDocumento'] ?? [];

    $fecha = ($ident['fecEmi'] ?? '') . ' ' . ($ident['horEmi'] ?? '');

    $totalPagar   = $resumen['totalPagar']   ?? 0;
    $subTotal     = $resumen['subTotal']     ?? 0;
    $totalIva     = $resumen['totalIva']     ?? 0;
    $totalGravada = $resumen['totalGravada'] ?? 0;

    // Tributo de turismo (código 59) si viene en resumen.tributos
    $turismo = 0.00;
    if (!empty($resumen['tributos']) && is_array($resumen['tributos'])) {
        foreach ($resumen['tributos'] as $t) {
            if (($t['codigo'] ?? null) === '59') { $turismo += (float)($t['valor'] ?? 0); }
        }
    }

    // Pagos: mostrar "Contado" si condicionOperacion === 1, de lo contrario "Crédito"
    $condicion = ((int)($resumen['condicionOperacion'] ?? 1) === 1) ? 'Contado' : 'Crédito';
    $pagoPrimario = null;
    if (!empty($resumen['pagos']) && is_array($resumen['pagos'])) {
        $pagoPrimario = $resumen['pagos'][0] ?? null; // Tomamos el primero
    }

    // Helpers
    $fmt = fn($n) => number_format((float)$n, 2, '.', ',');
    $upper = fn($s) => mb_strtoupper((string)$s, 'UTF-8');

    // Formateo de NIT con guiones si viene sin ellos (14 dígitos -> 4-6-3-1)
    $fmtNIT = function ($nit) {
        $nit = preg_replace('/[^0-9]/', '', (string)$nit);
        if (strlen($nit) === 14) {
            return substr($nit,0,4).'-'.substr($nit,4,6).'-'.substr($nit,10,3).'-'.substr($nit,13,1);
        }
        return $nit;
    };

    // Construir cadenas seguras para evitar @if anidados
    $emisorDir = trim(($emisor['direccion']['complemento'] ?? ''));
    if (!empty($emisor['direccion']['municipio'])) { $emisorDir .= (strlen($emisorDir)?', ':'').('Municipio '.$emisor['direccion']['municipio']); }
    if (!empty($emisor['direccion']['departamento'])) { $emisorDir .= (strlen($emisorDir)?', ':'').$emisor['direccion']['departamento']; }
    if (!empty($emisor['telefono'])) { $emisorDir .= (strlen($emisorDir)?' | ':'').('Tel: '.$emisor['telefono']); }

    $receptorDir = trim(($receptor['direccion']['complemento'] ?? ''));
    if (!empty($receptor['direccion']['municipio'])) { $receptorDir .= (strlen($receptorDir)?', ':'').('Municipio '.$receptor['direccion']['municipio']); }
    if (!empty($receptor['direccion']['departamento'])) { $receptorDir .= (strlen($receptorDir)?', ':'').$receptor['direccion']['departamento']; }

    $correoEmisor = $emisor['correo'] ?? null;
    $telReceptor  = $receptor['telefono'] ?? null;
    $correoReceptor = $receptor['correo'] ?? null;
    $numDocReceptor = $receptor['numDocumento'] ?? null;
@endphp
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Versión legible DTE</title>
    <style>
        * { box-sizing: border-box; }
        html, body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        
        .muted { color: #444; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: 700; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
        .hr { border-top: 1px solid #000; margin: 8px 0; }
        .hr-light { border-top: 1px solid #ccc; margin: 8px 0; }
        .box { border: 1px solid #000; padding: 8px; }
        .small { font-size: 11px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #000; padding: 6px; }
        .table th { background: #f2f2f2; }
        .w-10 { width: 10%; }
        .w-12 { width: 12%; }
        .w-15 { width: 15%; }
        .w-20 { width: 20%; }
        .w-30 { width: 30%; }
    </style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

</head>
<body>

<div class="container">
<div class="row">
    <div class="col-12">
    {{-- Encabezado --}}
    <div class="mb-2">
       
        <div>{{ $emisor['nombre'] ?? '' }}</div>
        <div class="small muted">{{ $emisorDir }}</div>
        <div class="hr"></div>
    </div>

    <div class="mb-2 text-center fw-bold" style="font-size:16px;">FACTURA ELECTRÓNICA</div>

    <div class="grid-3 mb-3">
        <div class="box">
            <div><span class="fw-bold">N° Control:</span> {{ $ident['numeroControl'] ?? '' }}</div>
            <div><span class="fw-bold">Código:</span> {{ $ident['codigoGeneracion'] ?? '' }}</div>
            <div><span class="fw-bold">Sello:</span> {{ $dte['selloRecibido'] ?? '' }}</div>
            <div><span class="fw-bold">Fecha:</span> {{ $fecha }}</div>
        </div>
        
    </div>
    </div>
    </div>

<div class="row">

    <div class="col box">
            <div class="fw-bold">DATOS DEL EMISOR</div>
            <div>{{ $emisor['nombre'] ?? '' }}</div>
            <div>NIT: {{ $fmtNIT($emisor['nit'] ?? '') }}</div>
            <div>NRC: {{ $emisor['nrc'] ?? '' }}</div>
            <div>Actividad: {{ $emisor['descActividad'] ?? '' }}</div>
            @if(!empty($correoEmisor))
                <div>Correo: {{ $correoEmisor }}</div>
            @endif
    </div>

    <div class="col box">
        <div class="fw-bold">DATOS DEL CLIENTE</div>
        <div>Nombre: {{ $receptor['nombre'] ?? '' }}</div>
        @if(!empty($numDocReceptor))
            <div>Documento: {{ $numDocReceptor }}</div>
        @endif
        <div>Dirección: {{ $receptorDir }}</div>
        @if(!empty($telReceptor))
            <div>Teléfono: {{ $telReceptor }}</div>
        @endif
        @if(!empty($correoReceptor))
            <div>Correo: {{ $correoReceptor }}</div>
        @endif
    </div>
</div>
    {{-- Detalle --}}
    <table class="table mb-3">
        <thead>
        <tr>
            <th class="w-10">CANT.</th>
            <th>DESCRIPCIÓN</th>
            <th class="w-15 text-right">PRECIO<br>UNIT.</th>
            <th class="w-15 text-right">VENTAS NO<br>SUJETAS</th>
            <th class="w-15 text-right">VENTAS<br>EXENTAS</th>
            <th class="w-15 text-right">TOTAL</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $it)
            @php
                $cant   = $it['cantidad'] ?? 1;
                $desc   = $it['descripcion'] ?? '';
                $punit  = $it['precioUni'] ?? 0;
                $noSuj  = $it['ventaNoSuj'] ?? 0;
                $exenta = $it['ventaExenta'] ?? 0;
                // total por línea: lo más fiel a la "ventaGravada + exenta + noSuj" si existiera
                $totalLinea = ($it['ventaGravada'] ?? 0) + $exenta + $noSuj;
            @endphp
            <tr>
                <td class="text-center">{{ (int)$cant }}</td>
                <td class="text-left">{{ $desc }}</td>
                <td class="text-right">${{ $fmt($punit) }}</td>
                <td class="text-right">${{ $fmt($noSuj) }}</td>
                <td class="text-right">${{ $fmt($exenta) }}</td>
                <td class="text-right">${{ $fmt($totalLinea) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">Sin ítems</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- Totales y pago --}}
    <div class="mb-2"><span class="fw-bold">TOTAL EN LETRAS:</span> {{ $upper($resumen['totalLetras'] ?? '') }}</div>

    <div class="grid-2">
        <div class="box">
            <div class="fw-bold">CONDICIÓN DE PAGO:</div>
            <div>{{ $condicion }}</div>
            @if(!empty($pagoPrimario))
                @php $etq = (($pagoPrimario['codigo'] ?? '01') === '01') ? 'Efectivo' : 'Pago'; @endphp
                <div>- {{ $etq }}: ${{ $fmt($pagoPrimario['montoPago'] ?? $totalPagar) }}</div>
            @endif
        </div>
        <div class="box">
            <div class="grid-2">
                <div>SUMAS:</div>
                <div class="text-right">${{ $fmt($subTotal ?: $totalGravada) }}</div>
                <div class="fw-bold">TOTAL A PAGAR:</div>
                <div class="text-right fw-bold">${{ $fmt($totalPagar) }}</div>
            </div>
        </div>
    </div>

    <div class="mt-3 small muted">NOTAS IMPORTANTES</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>
