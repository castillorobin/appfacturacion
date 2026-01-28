<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<?php

use App\Models\DocumentoDTE;
use App\Models\ConteoDTE;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
       // mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }
}

function numeroALetras($numero) {
    $unidad = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince',
        'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte'
    ];

    $decenas = [
        '', '', 'veinti', 'treinta', 'cuarenta', 'cincuenta',
        'sesenta', 'setenta', 'ochenta', 'noventa'
    ];

    $centenas = [
        '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos',
        'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
    ];

    if ($numero == 0) return 'Cero dólares 00/100';

    $entero = floor($numero);
    $centavos = round(($numero - $entero) * 100);

    $letras = '';

    if ($entero >= 1000000) {
        $millones = floor($entero / 1000000);
        $letras .= numeroALetras($millones) . ' millón' . ($millones > 1 ? 'es' : '') . ' ';
        $entero %= 1000000;
    }

    if ($entero >= 1000) {
        $miles = floor($entero / 1000);
        if ($miles == 1) {
            $letras .= 'mil ';
        } else {
            $letras .= numeroALetras($miles) . ' mil ';
        }
        $entero %= 1000;
    }

    if ($entero > 0) {
        if ($entero == 100) {
            $letras .= 'cien';
        } else {
            $c = floor($entero / 100);
            $d = floor(($entero % 100) / 10);
            $u = $entero % 10;

            $letras .= $centenas[$c];

            if ($d == 1 || ($d == 2 && $u == 0)) {
                $letras .= ($c > 0 ? ' ' : '') . $unidad[$d * 10 + $u];
            } elseif ($d == 2) {
                $letras .= 'i' . $unidad[$u];
            } elseif ($d > 2) {
                $letras .= ($c > 0 ? ' ' : '') . $decenas[$d];
                if ($u > 0) {
                    $letras .= ' y ' . $unidad[$u];
                }
            } elseif ($u > 0) {
                $letras .= ($c > 0 ? ' ' : '') . $unidad[$u];
            }
        }
    }

    $letras = trim(ucfirst($letras)) . ' dólares';

    $letras .= ' con ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100';

    return $letras;
}

function sacarivas($detalles){
    $iva = 0;
    foreach ($detalles as $detalle) {
        $base = $detalle->total / 1.13;
        $iva += round($base * 0.13, 2);
    }
    return $iva;
}

function sacartotal($detalles){
    $totalBase = 0;
    foreach ($detalles as $detalle) {
        // Suponiendo que el total viene con IVA incluido
        $base = $detalle->total / 1.13;
        $totalBase += round($base, 2);
    }
    return $totalBase;
}

// Clases para estructurar el DTE
class Identificacion {
    public $version = 3;
    public $ambiente = "01";
    public $tipoDte = "03"; 
    public $numeroControl;
    public $codigoGeneracion;
    public $tipoModelo = 1;
    public $tipoOperacion = 1;
    public $tipoContingencia = null;
    public $motivoContin = null;
    public $fecEmi;
    public $horEmi;
    public $tipoMoneda = "USD";
}

class Direccion {
    public $departamento;
    public $municipio;
    public $complemento;
}

class Emisor {
    public $nit;
    public $nrc;
    public $nombre;
    public $codActividad;
    public $descActividad;
    public $nombreComercial;
    public $tipoEstablecimiento;
    public $direccion;
    public $telefono;
    public $codEstableMH;
    public $codEstable;
    public $codPuntoVentaMH;
    public $codPuntoVenta;
    public $correo;
}

class Receptor {
    public $nit;
    public $nrc;
    public $nombre;
    public $nombreComercial;
    public $codActividad;
    public $descActividad;
    public $direccion;
    public $telefono;
    public $correo;
}

class ItemDocumento {
    public $numItem;
    public $tipoItem;
    public $numeroDocumento;
    public $cantidad;
    public $codigo;
    public $codTributo;
    public $uniMedida;
    public $descripcion;
    public $precioUni;
    public $montoDescu;
    public $ventaNoSuj;
    public $ventaExenta;
    public $ventaGravada;
    public $tributos;
    public $psv;
    public $noGravado;
}

class Pago {
    public $codigo;
    public $montoPago;
    public $referencia;
    public $periodo;
    public $plazo;
}

class Tributo {
    public $codigo;
    public $descripcion;
    public $valor;
}

class Resumen {
    public $totalNoSuj;
    public $totalExenta;
    public $totalGravada;
    public $subTotalVentas;
    public $descuNoSuj;
    public $descuExenta;
    public $descuGravada;
    public $porcentajeDescuento;
    public $totalDescu;
    public $subTotal;
    public $ivaRete1;
    public $ivaPerci1;
    public $reteRenta;
    public $montoTotalOperacion;
    public $totalNoGravado;
    public $totalPagar;
    public $totalLetras;
    public $saldoFavor;
    public $condicionOperacion;
    public $pagos;
    public $tributos;
    public $numPagoElectronico;
}

class Extension {
    public $nombEntrega;
    public $docuEntrega;
    public $nombRecibe;
    public $docuRecibe;
    public $observaciones;
    public $placaVehiculo;
}

class DocumentoTributarioElectronico {
    public $identificacion;
    public $documentoRelacionado;
    public $emisor;
    public $receptor;
    public $ventaTercero;
    public $cuerpoDocumento;
    public $resumen;
    public $extension;
    public $otrosDocumentos;
    public $apendice;
}
date_default_timezone_set('America/El_Salvador');
$fecha_actual = date("Y-m-d");
$hora_actual = date("h:i:s");
 
// Función para crear el DTE
function crearDTE($fecha_actual, $hora_actual, $detalles, $cliente, $actividad_descripcion, $conteo) {
    $correlativo = str_pad($conteo->conteo + 1, 15, '0', STR_PAD_LEFT);
$numeroControl = "DTE-03-M001P001-" . $correlativo;
    $dte = new DocumentoTributarioElectronico();
   // dd($numeroControl);
    // Configurar identificación
    $dte->identificacion = new Identificacion();
    $dte->identificacion->numeroControl = $numeroControl; //DTE-01-F0000001-000080000000263
    $dte->identificacion->codigoGeneracion = getGUID(); //"7DEEF8AF-7DF6-476F-B9AE-47CA46035F1B";
    $dte->identificacion->fecEmi = $fecha_actual;
    $dte->identificacion->horEmi = $hora_actual;
    
     // Configurar emisor
    $dte->emisor = new Emisor();
    $dte->emisor->nit = "050080424";
    $dte->emisor->nrc = "3100846";
    $dte->emisor->nombre = "ERICK ROBERTO CLIMACO ARRIAGA";
    $dte->emisor->codActividad = "53100";
    $dte->emisor->descActividad = "Servicios de Correo Nacional";
    $dte->emisor->nombreComercial = "ERICK CLIMACO";
    $dte->emisor->tipoEstablecimiento = "02";
    $dte->emisor->direccion = new Direccion();
    $dte->emisor->direccion->departamento = "06";
    $dte->emisor->direccion->municipio = "23";
    $dte->emisor->direccion->complemento = "Calle Sisimiles, costado norte de Metrocentro, Local 3-1";
    $dte->emisor->telefono = "7457-6280";
    $dte->emisor->codEstableMH = null;
    $dte->emisor->codEstable = null;
    $dte->emisor->codPuntoVentaMH = null;
    $dte->emisor->codPuntoVenta = null;
    $dte->emisor->correo = "facturacionmeloexpress@gmail.com";

    // Configurar receptor
    $dte->receptor = new Receptor();
    $dte->receptor->nit = $cliente[0]->nit;
    $dte->receptor->nrc = $cliente[0]->nrc;
    $dte->receptor->nombre = $cliente[0]->nombre;
    $dte->receptor->nombreComercial = $cliente[0]->nombre_comercial;
    $dte->receptor->codActividad = $cliente[0]->actividad_economica_id;
    $dte->receptor->descActividad = $actividad_descripcion;
    $dte->receptor->direccion = new Direccion();
    $dte->receptor->direccion->departamento = "06";//$cliente[0]->departamento;
    $dte->receptor->direccion->municipio = "23";//$cliente[0]->municipio;
    $dte->receptor->direccion->complemento = "Avenida";//$cliente[0]->direccion;
    $dte->receptor->telefono = $cliente[0]->telefono;
    $dte->receptor->correo = $cliente[0]->correo;


   $cuerpo = [];
$totalGravada = 0;
$itemnum = 1;

foreach ($detalles as $detalle) {
    $item = new ItemDocumento();
    $item->numItem = $itemnum++;
    $item->tipoItem = 3;
    $item->numeroDocumento = null;
    $item->cantidad = $detalle->cantidad;

    $precioConIVA = round($detalle->total, 2);
   
    $baseSinIVA = round($precioConIVA / 1.13, 2);
     
    $precioUnitarioBase = $baseSinIVA / $item->cantidad;
    
    $item->codigo = "27";
    $item->codTributo = null;
    $item->uniMedida = 59;
    $item->descripcion = $detalle->descripcion;
    $item->precioUni = round($precioUnitarioBase, 2);
//dd(round($item->precioUni ));
    $item->montoDescu = 0.00;
    $item->ventaNoSuj = 0.00;
    $item->ventaExenta = 0.00;

    $item->ventaGravada = round($baseSinIVA, 2);
   
    $item->psv = $item->ventaGravada;
    
    $item->noGravado = 0.00;
    $item->tributos = ["20"];

    $totalGravada += $item->ventaGravada;
    $cuerpo[] = $item;
}

$dte->cuerpoDocumento = $cuerpo;


// Calcular totales base y IVA
$totalBase = round(sacartotal($detalles), 2);
$totalIVA = round($totalBase * 0.13, 2);
$totalPagar = $totalBase + $totalIVA;

$dte->resumen = new Resumen();
$dte->resumen->totalNoSuj = 0.00;
$dte->resumen->totalExenta = 0.00;
$dte->resumen->totalGravada = round($totalGravada, 2);
$dte->resumen->subTotalVentas = round($totalGravada, 2);
$dte->resumen->descuNoSuj = 0.00;
$dte->resumen->descuExenta = 0.00;
$dte->resumen->descuGravada = 0.00;
$dte->resumen->porcentajeDescuento = 0.00;
$dte->resumen->totalDescu = 0.00;
$dte->resumen->subTotal = round($totalGravada, 2);
$dte->resumen->ivaRete1 = 0.00;
$dte->resumen->ivaPerci1 = 0.00;
$dte->resumen->reteRenta = 0.00;
$dte->resumen->montoTotalOperacion = round($totalPagar, 2);
$dte->resumen->totalNoGravado = 0.00;
$dte->resumen->totalPagar = round($totalPagar, 2);
$dte->resumen->totalLetras = numeroALetras($totalPagar);
$dte->resumen->saldoFavor = 0.00;
$dte->resumen->condicionOperacion = 1;
$dte->resumen->pagos = null;

$dte->resumen->tributos = [
    [
        "codigo" => "20",
        "descripcion" => "Impuesto al Valor Agregado 13%",
        "valor" => $totalIVA
    ]
];

$dte->resumen->numPagoElectronico = null;

    // Configurar extensión
    $dte->extension = new Extension();
    $dte->extension->nombEntrega = null;
    $dte->extension->docuEntrega = null;
    $dte->extension->nombRecibe = null;
    $dte->extension->docuRecibe = null;
    $dte->extension->observaciones = null;
    $dte->extension->placaVehiculo = null;

    return $dte;
}

// Función para enviar DTE a la API

//para pruebas en ambiente de produccion cambiar ambiente a 01
function enviarDTEAPI($dte, $cliente) {
    $datos = [
        'Usuario' => "050080424",
        'Password' => "Melo2025!",
        'Ambiente' => '01',
        'DteJson' => json_encode($dte),
        'Nit' => "06171401911015",
        'PasswordPrivado' => 'Meloexp1.',
        'TipoDte' => '03',
        'CodigoGeneracion' => $dte->identificacion->codigoGeneracion,
        'NumControl' => $dte->identificacion->numeroControl,
        'VersionDte' => 3,
        //'CorreoCliente' => "poncemarito2019@gmail.com"
        'CorreoCliente' => $cliente[0]->correo
        //'CorreoCliente' => $factura[0]->correo
    ];
   
    /*
//para pruebas en pruebas cambiar ambiente a 00
    function enviarDTEAPI($dte, $cliente) {
    $datos = [
        'Usuario' => "050080424",
        'Password' => "Melo2025.",
        'Ambiente' => '00',
        'DteJson' => json_encode($dte),
        'Nit' => "06171401911015",
        'PasswordPrivado' => 'Meloexp1.',
        'TipoDte' => '03',
        'CodigoGeneracion' => $dte->identificacion->codigoGeneracion,
        'NumControl' => $dte->identificacion->numeroControl,
        'VersionDte' => 3,
        //'CorreoCliente' => "poncemarito2019@gmail.com"
        'CorreoCliente' => $cliente[0]->correo
        //'CorreoCliente' => $factura[0]->correo
    ];

    */

   // echo "<pre>JSON enviado a la API:<br>" . json_encode($datos, JSON_PRETTY_PRINT) . "</pre>";

    $ch = curl_init('http://54.152.201.240:7122/api/procesar-dte');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        throw new Exception("Error cURL: $error (Código: $errno)");
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpCode != 200) {
        throw new Exception("Error al procesar DTE: " . $response . " (HTTP $httpCode)");
    }

    return json_decode($response);
}


// Ejemplo de uso
// Iniciar proceso automáticamente al abrir el archivo desde el navegador
try {
    echo "Iniciando generación de DTE...<br>";
    $dte = crearDTE($fecha_actual, $hora_actual, $detalles, $cliente, $actividad_descripcion, $conteo);
    echo "DTE generado correctamente.<br>";
    echo "Iniciando transferencia a la API...<br>";
    $respuestaAPI = enviarDTEAPI($dte, $cliente);
    echo "Respuesta recibida de la API.<br>";
    // Imprimir sello de recepción antes de enviar el correo
   if (isset($respuestaAPI->selloRecibido)) {
    echo "Sello de recepción: " . $respuestaAPI->selloRecibido . "<br>";
} elseif (isset($respuestaAPI->SelloRecepcion)) {
    echo "Sello de recepción (SelloRecepcion): " . $respuestaAPI->SelloRecepcion . "<br>";
} elseif (isset($dte->identificacion->codigoGeneracion)) {
    echo "Código de generación: " . $dte->identificacion->codigoGeneracion . "<br>";
}
    echo "Proceso completado exitosamente.<br>";

    if (isset($respuestaAPI->selloRecibido) || isset($respuestaAPI->SelloRecepcion)) {
    // Determinar el tipo de DTE para actualizar el conteo
    $tipo = $dte->identificacion->tipoDte;

    // Obtener el registro actual
    $conteoActual = ConteoDTE::where('tipo', $tipo)->lockForUpdate()->first();

    if ($conteoActual) {
        $conteoActual->conteo += 1;
        $conteoActual->save();
    }
}

  // Almacenar datos del DTE
$dteArray = json_decode(json_encode($dte), true);
 // Datos de la respuesta MH
    $codigoGeneracion = $respuestaAPI->codigoGeneracion ?? ($dteArray['identificacion']['codigoGeneracion'] ?? (string) Str::uuid());
    $numControl       = $respuestaAPI->numControl       ?? ($dteArray['identificacion']['numeroControl'] ?? null);
    $selloRecibido    = $respuestaAPI->selloRecibido    ?? null;
    $jwsFirmado       = $respuestaAPI->dteFirmado       ?? null;

    // 1) Guardar JSON ORIGINAL
    $rutaOriginal = "dtes_json/original_{$codigoGeneracion}.json";
    Storage::put($rutaOriginal, json_encode($dteArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // 2) Construir JSON LEGIBLE PARA CONTADOR
    $legible = $dteArray;
    $legible['identificacion']['codigoGeneracion'] = $codigoGeneracion;
    if ($numControl) {
        $legible['identificacion']['numeroControl'] = $numControl;
    }

    //  Ordenar: primero firmaElectronica, luego selloRecibido
    if ($jwsFirmado) {
        $legible['firmaElectronica'] = $jwsFirmado;
    }
    if ($selloRecibido) {
        unset($legible['selloRecibido']); // por si acaso existe
        // Forzar sello al final
        $legible = array_merge($legible, ['selloRecibido' => $selloRecibido]);
    }

    $rutaLegible = "dtes_json/legible_{$codigoGeneracion}.json";
   // Storage::put($rutaLegible, json_encode($legible, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    Storage::put($rutaLegible, json_encode($legible, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    // 3) Guardar JWS firmado crudo
    $rutaFirmado = null;
    if ($jwsFirmado) {
        $rutaFirmado = "dtes_json/firmado_{$codigoGeneracion}.json";
        Storage::put($rutaFirmado, $jwsFirmado);
    }


/*
// 4) Generar PDF versión legible para entrega
$pdf = Pdf::loadView('dtes.plantilla_pdf', ['dte' => $legible]); // $legible = tu JSON legible
$rutaPdf = "dtes_pdfs/dte_{$codigoGeneracion}.pdf";
Storage::put($rutaPdf, $pdf->output());
*/

// 5) Persistir en BD
DocumentoDTE::create([
'sello_recibido' => $selloRecibido,
'codigo_generacion' => $codigoGeneracion,
'numero_control' => $numControl,
'factura' => $detalles[0]->coticode ?? null,
'fecha_generacion' => now(),
'tipo_dte' => $dteArray['identificacion']['tipoDte'] ?? null,
'json_original_path' => $rutaOriginal,
'json_legible_path' => $rutaLegible,
'json_firmado_path' => $rutaFirmado,
//'pdf_path' => $rutaPdf,
]);

echo '
<p></p>

&nbsp; &nbsp; &nbsp;
<a href="/dtes" class="btn btn-danger">Regresar </a>
';
//Termina Almacenar datos del DTE

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

?>
