<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\DocumentoDTE;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\Proveedor;
use App\Models\Producto;
//use Barryvdh\DomPDF\Facade\Pdf;



class DTEController extends Controller {
public function index(Request $request) {
$query = DocumentoDTE::query();

    // Filtro por rango de fechas si se envía
    if ($request->filled('desde') && $request->filled('hasta')) {
        $query->whereBetween('created_at', [
            Carbon::parse($request->desde)->startOfDay(),
            Carbon::parse($request->hasta)->endOfDay()
        ]);
    } else {
        // Por defecto mostrar solo los de HOY (en la zona horaria configurada)
        $query->whereBetween('created_at', [
            Carbon::today()->startOfDay(),
            Carbon::today()->endOfDay()
        ]);
    }

    $dtes = $query->orderByDesc('created_at')->paginate(15)->withQueryString();;

    return view('dtes.index', compact('dtes'));


}


public function descargarJson($id) {
$dte = DocumentoDTE::findOrFail($id);
// Preferir entregar el "legible" si existe, de lo contrario el firmado
$path = $dte->json_legible_path ?: $dte->json_firmado_path;
//dd($path);
return Storage::download($path);

}


public function verPdf($id) {
 $dte = \App\Models\DocumentoDTE::findOrFail($id);

    // 1) Carga el JSON legible desde storage
    if (!$dte->json_legible_path || !Storage::exists($dte->json_legible_path)) {
        abort(404, 'No hay JSON legible para generar PDF.');
    }

    $json = Storage::get($dte->json_legible_path);
    $legible = json_decode($json, true);

    if (!is_array($legible)) {
        abort(500, 'JSON legible inválido.');
    }

    // 2) Genera PDF desde la vista (sin guardar en disco)
    $pdf = Pdf::loadView('dtes.plantilla_pdf', ['dte' => $legible]);

    // 3) Devuelve el PDF en línea (inline)
    $nombre = 'dte_'.$legible['identificacion']['codigoGeneracion'].'.pdf';
    return $pdf->stream($nombre); // o ->download($nombre) si prefieres forzar descarga
}


// Lógica para guardar JSON firmado y generar PDF tras envío exitoso
public function guardarDteExitoso($dte, $respuestaAPI, $jsonFirmado) {
$codigo = $dte->identificacion->codigoGeneracion ?? Str::uuid();
$nombreArchivoJson = "dte_{$codigo}.json";
$rutaJson = "dtes_json/{$nombreArchivoJson}";


Storage::put($rutaJson, json_encode($jsonFirmado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));


// Generar PDF usando DomPDF
$pdf = Pdf::loadView('dtes.plantilla_pdf', ['dte' => $dte]);
$nombreArchivoPdf = "dte_{$codigo}.pdf";
$rutaPdf = "dtes_pdfs/{$nombreArchivoPdf}";
Storage::put($rutaPdf, $pdf->output());


DocumentoDTE::create([
'sello_recibido' => $respuestaAPI->selloRecibido ?? null,
'codigo_generacion' => $codigo,
'numero_control' => $dte->identificacion->numeroControl ?? null,
'factura' => $dte->coticode ?? null,
'fecha_generacion' => now(),
'json_firmado_path' => $rutaJson,
'pdf_path' => $rutaPdf,
]);
}




public function descargarJsonLote(Request $request)
{
    $tipo = $request->input('tipo', 'legible'); // legible | firmado | original
    if (!in_array($tipo, ['legible','firmado','original'])) {
        $tipo = 'legible';
    }

    // === armar query con mismos filtros que tu index ===
    $query = DocumentoDTE::query();

    $desde = $request->input('desde');
    $hasta = $request->input('hasta');
    $tz    = 'America/El_Salvador';

    if ($desde || $hasta) {
        $desdeC = $desde ? Carbon::parse($desde, $tz)->startOfDay() : Carbon::minValue();
        $hastaC = $hasta ? Carbon::parse($hasta, $tz)->endOfDay()   : Carbon::now($tz)->endOfDay();
        $query->whereBetween('created_at', [$desdeC, $hastaC]);
    } else {
        // si quieres restringir por defecto a HOY
        $query->whereBetween('created_at', [Carbon::today($tz)->startOfDay(), Carbon::today($tz)->endOfDay()]);
    }

    // === preparar ZIP temporal ===
    Storage::makeDirectory('tmp');
    $zipName = 'dtes_json_'.now($tz)->format('Ymd_His').'.zip';
    $zipPath = storage_path('app/tmp/'.$zipName);

    $zip = new \ZipArchive();
    if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
        abort(500, 'No se pudo crear el ZIP.');
    }

    // Manifest de faltantes
    $faltantes = [];

    // === recorrer TODOS los registros filtrados (no solo la página) ===
    $query->orderBy('id')->chunkById(500, function($chunk) use ($zip, $tipo, &$faltantes) {
        foreach ($chunk as $doc) {
            // elegir la ruta según el tipo
            $ruta = match ($tipo) {
                'firmado'  => $doc->json_firmado_path,
                'original' => $doc->json_original_path,
                default    => $doc->json_legible_path,
            };

            // nombre de archivo amigable
            $base = trim(($doc->codigo_generacion ?: 'SIN_CODIGO'));
            $numc = trim(($doc->numero_control ?: 'SIN_NUMCTRL'));
            $fname = sprintf('%s_%s.json', $base, $numc);

            if ($ruta && Storage::exists($ruta)) {
                // ruta absoluta del storage
                $abs = Storage::path($ruta);
                $zip->addFile($abs, $fname);
            } else {
                $faltantes[] = $fname.' => no encontrado ('.$ruta.')';
            }
        }
    });

    if (!empty($faltantes)) {
        $zip->addFromString('faltantes.txt', implode(PHP_EOL, $faltantes));
    }

    $zip->close();

    // === descarga y elimina ===
    return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
}


public function anular(Request $request, DocumentoDTE $dte)
{
    $motivo = $request->motivo ?? 'Anulación solicitada';
    $codigoGeneracion = $dte->codigo_generacion;
     // 1. Armar el JSON exacto como lo hiciste en Postman
     $legible = json_decode(Storage::get("dtes_json/legible_{$codigoGeneracion}.json"), true);
//dd($legible);
    $dteJson = [
        "identificacion" => [
            "version" => 2,
            "ambiente" => "01",
            "codigoGeneracion" => strtoupper(Str::uuid()->toString()),
            "fecAnula" => now()->format('Y-m-d'),
            "horAnula" => now()->format('H:i:s'),
        ],
        "emisor" => [
            "nit" => "050080424",
            "nombre" => "ERICK ROBERTO CLIMACO ARRIAGA",
            "tipoEstablecimiento" => "02",
            "nomEstablecimiento" => "ERICK CLIMACO",
            "codEstableMH" => null,
            "codEstable" => "B001",
            "codPuntoVentaMH" => null,
            "codPuntoVenta" => "P001",
            "telefono" => "7457-6280",
            "correo" => "facturacionmeloexpress@gmail.com"
        ],
        "documento" => [
            "tipoDte" => $legible['identificacion']['tipoDte'],
            "codigoGeneracion" => $legible['identificacion']['codigoGeneracion'],
            "selloRecibido" => $legible['selloRecibido'] ?? null,
            "numeroControl" => $legible['identificacion']['numeroControl'],
            "fecEmi" => $legible['identificacion']['fecEmi'],
            "montoIva" => $legible['resumen']['totalIva'],
            "codigoGeneracionR" => null,
            "tipoDocumento" => $legible['receptor']['tipoDocumento'],
            "numDocumento" => $legible['receptor']['numDocumento'],
            "nombre" => $legible['receptor']['nombre'],
            "telefono" => $legible['receptor']['telefono'] ?? null,
            "correo" => $legible['receptor']['correo'] ?? null,
        ],
        "motivo" => [
            "tipoAnulacion" => 2,
            "motivoAnulacion" => $motivo,
            "nombreResponsable" => "ADMIN",
            "tipDocResponsable" => "13",
            "numDocResponsable" => "000000000",
            "nombreSolicita" => "ADMIN",
            "tipDocSolicita" => "13",
            "numDocSolicita" => "000000000"
        ]
    ];

    $payload = [
        "Usuario" => "050080424",
        "Password" => "Melo2025!",
        "Ambiente" => "01",
        "DteJson" => json_encode($dteJson),
        "Nit" => "06171401911015",
        "PasswordPrivado" => 'Meloexp1.'
    ];



    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('http://54.152.201.240:7122/api/anular-dte', $payload);

        $result = $response->json();

        if (!$response->successful()) {
            return back()->with('error', 'Error en envío de DTE (anulación): ' . json_encode($result));
        }

        // Opcional: guardar confirmación de anulación
        $dte->update([
            'estado' => 'anulado',
            'fecha_anulacion' => now(),
        ]);

        return back()->with('success', 'DTE anulado correctamente.');

    } catch (\Exception $e) {
        return back()->with('error', 'Error al anular DTE: ' . $e->getMessage());
    }
}

public function creandosujeto()
{
    $productos = Producto::all();
    $proveedores = Proveedor::all();
    return view('facturas.createsujeto', compact('proveedores', 'productos'));
}
}



