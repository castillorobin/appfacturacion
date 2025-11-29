<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<?php
use App\Models\DocumentoDTE;

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
        $iva += $detalle->preciouni;

    }
   $ivatotal = (($iva / 1.13) * 0.13);
    return $ivatotal;
}

function sacartotal($detalles){
    $total = 0;
    foreach ($detalles as $detalle) {
        $total += $detalle->preciouni;

    }
    return $total;
}



// Clases para estructurar el DTE
class Identificacion {
    public $version = 1;
    public $ambiente = "00";
    public $tipoDte = "14"; 
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
    //public $nombreComercial;
   // public $tipoEstablecimiento;
    public $direccion;
    public $telefono;
    public $codEstableMH;
    public $codEstable;
    public $codPuntoVentaMH;
    public $codPuntoVenta;
    public $correo;
    //public $tributos;
}

class sujetoExcluido {
    //public $nrc;
    public $nombre;
    public $codActividad;
    public $descActividad;
    public $direccion;
    public $telefono;
    public $correo;
}

class ItemDocumento {
    public $numItem;
    public $tipoItem;
    //public $numeroDocumento;
    public $cantidad;
    public $codigo;
    //public $codTributo;
    public $uniMedida;
    public $descripcion;
    public $precioUni;
    public $montoDescu;
    //public $reteRenta;
   // public $ventaNoSuj;
    //public $ventaExenta;
    //public $ventaGravada;
    //public $tributos;
    //public $psv;
    //public $ivaItem;
    //public $noGravado;
    public $compra;

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
    public $totalDescu;
    public $subTotal;
    public $ivaRete1;
    public $reteRenta;  
    public $totalPagar;
    public $totalLetras;
    public $condicionOperacion;
    public $pagos;
    public $totalCompra;
    public $descu;
    public $observaciones;
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
    //public $documentoRelacionado;
    public $emisor;
    public $sujetoExcluido;
    //public $ventaTercero;
    public $cuerpoDocumento;
    public $resumen;
    //public $extension;
    //public $otrosDocumentos;
    public $apendice;
}
date_default_timezone_set('America/El_Salvador');
$fecha_actual = date("Y-m-d");
$hora_actual = date("h:i:s");

// Función para crear el DTE
function crearDTE($fecha_actual, $cliente, $hora_actual, $detalles) {

 $paradte = 70000000000 + $detalles[0]->id;
    
    $dte = new DocumentoTributarioElectronico();
    
    // Configurar identificación
    $dte->identificacion = new Identificacion();
    $dte->identificacion->numeroControl = "DTE-14-M001P001-0000". $paradte;  //DTE-01-F0000001-000080000000263
    //dd( $dte->identificacion->numeroControl);
    $dte->identificacion->codigoGeneracion = getGUID(); //"7DEEF1AF-7DF7-436F-B9AE-47CA46035F1B";
    $dte->identificacion->fecEmi = $fecha_actual;
    $dte->identificacion->horEmi = $hora_actual;
    
    // Configurar emisor
    $dte->emisor = new Emisor();
    $dte->emisor->nit = "032267824";
    $dte->emisor->nrc = "2193320";
    $dte->emisor->nombre = "ROBIN ANTONIO CASTILLO SAAVEDRA";
    $dte->emisor->codActividad = "96092";
    $dte->emisor->descActividad = "Servicios n.c.p.";
    //$dte->emisor->nombreComercial = "VILMA JANNET GODOY MENDOZA";
    //$dte->emisor->tipoEstablecimiento = "02";
    $dte->emisor->direccion = new Direccion();
    $dte->emisor->direccion->departamento = "02";
    $dte->emisor->direccion->municipio = "01";
    $dte->emisor->direccion->complemento = "PTO 23 VTA 10 AV SUR ENTRE 15 Y 17 CL PTE TERM DE BUSES FCO CL LA TERMINAL 116";
    $dte->emisor->telefono = "71902000";
    $dte->emisor->codEstableMH = null;
    $dte->emisor->codEstable = null;
    $dte->emisor->codPuntoVentaMH = null;
    $dte->emisor->codPuntoVenta = null;
    $dte->emisor->correo = "castillorobin11@gmail.com";

    // Configurar receptor
    $dte->sujetoExcluido = new sujetoExcluido();
    $dte->sujetoExcluido->tipoDocumento = "37";
    $dte->sujetoExcluido->numDocumento = $cliente->dui;
    $dte->sujetoExcluido->nombre = $cliente->nombre;
    $dte->sujetoExcluido->codActividad = "41001";
    $dte->sujetoExcluido->descActividad = "Clientes Frecuentes";
    $dte->sujetoExcluido->direccion = new Direccion();
    $dte->sujetoExcluido->direccion->departamento = "02";
    $dte->sujetoExcluido->direccion->municipio = "01";
    $dte->sujetoExcluido->direccion->complemento = $cliente->direccion;
    $dte->sujetoExcluido->telefono = $cliente->telefono;
    $dte->sujetoExcluido->correo = $cliente->correo;

$cuerpo = [];
$totalGravada = 0;
$itemnum = 1;
$totaliv = 0;
$renta = 0;
    foreach ($detalles as $detalle) {
   

    // Configurar cuerpo del documento
    $item = new ItemDocumento();
    $item->numItem = $itemnum;
    $itemnum += 1;
    $item->tipoItem = 1;
   // $item->numeroDocumento = null;
    $item->cantidad = $detalle->cantidad;
    $item->codigo = null;
    
    $item->uniMedida = 59;
    $item->descripcion = $detalle->descripcion;
    $item->precioUni = round($detalle->preciouni, 2);
    $item->montoDescu = 0;
  
    //$totalGravada += $item->precioUni;
$item->compra = round($detalle->preciouni * $detalle->cantidad, 2);
$totalGravada += $item->compra;
//$item->reteRenta = round($totalGravada * 0.10, 2);
//$renta += $item->reteRenta;
    $cuerpo[] = $item;
   
    $dte->cuerpoDocumento = [$item];
}
$dte->cuerpoDocumento = $cuerpo;


    // Configurar resumen
    $dte->resumen = new Resumen();
   // $dte->resumen->totalNoSuj = 0.00;
    //$dte->resumen->totalExenta = 0.00;
    $dte->resumen->totalCompra = round($totalGravada, 2);
   //dd(round(sacartotal($detalles), 2));
    $dte->resumen->descu = 0.00;
    $dte->resumen->totalDescu = 0.00;
    $dte->resumen->totalLetras = numeroALetras($totalGravada);
    $dte->resumen->subTotal = round($totalGravada, 2);
    $dte->resumen->ivaRete1 = 0.00;
    $dte->resumen->reteRenta = round($totalGravada * 0.10, 2);
    $dte->resumen->totalPagar = round($totalGravada - $dte->resumen->reteRenta, 2);
    $dte->resumen->condicionOperacion = 1;
    $dte->resumen->pagos = [
        [
            "codigo"=>"01",
            "montoPago"=>round($totalGravada - $dte->resumen->reteRenta, 2),
            "referencia"=>"0000",
            "periodo"=>null,
            "plazo"=>null
        ]
    ];
    
    $dte->resumen->observaciones = null;

    

    return $dte;
    
}
//$dte = crearDTE($fecha_actual, $cliente, $hora_actual, $detalles);
//echo "<pre>JSON generado:<br>" . json_encode($dte, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";



// Función para enviar DTE a la API

function enviarDTEAPI($dte, $cliente) {
    $datos = [
        'Usuario' => "032267824",
        'Password' => "Alexan24.",
        'Ambiente' => '00',
        'DteJson' => json_encode($dte),
        'Nit' => "05152308851012",
        'PasswordPrivado' => 'Pw6r$LbMw93',
        'TipoDte' => '14',
        'CodigoGeneracion' => $dte->identificacion->codigoGeneracion,
        'NumControl' => $dte->identificacion->numeroControl,
        'VersionDte' => 1,
        'CorreoCliente' => "poncemarito2019@gmail.com"
        //'CorreoCliente' => $cliente[0]->Correo
    ];

   // echo "<pre>JSON enviado a la API:<br>" . json_encode($datos, JSON_PRETTY_PRINT) . "</pre>";

    $ch = curl_init('http://98.89.90.33:7122/api/procesar-dte');
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
    $dte = crearDTE($fecha_actual, $cliente, $hora_actual, $detalles);
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
///header("Refresh: 3; url=https://xanadusistema.com/facturacion");

?>
