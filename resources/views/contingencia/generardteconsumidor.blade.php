<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<?php

use App\Models\ContingenciaDTE;

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
    public $tipoDte = "01"; 
    public $numeroControl;
    public $codigoGeneracion;
    public $tipoModelo = 2;
    public $tipoOperacion = 2;
    public $tipoContingencia = 3;
    public $motivoContin = "Falla en el sistema de facturación";
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
    //public $tributos;
}

class Receptor {
    public $nrc;
    public $tipoDocumento;
    public $numDocumento;
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
    public $ivaItem;
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
    public $totalIva;
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
function crearDTE($fecha_actual, $cliente, $hora_actual, $detalles) {

 $paradte = 70000000000 + $detalles[0]->id;
    
    $dte = new DocumentoTributarioElectronico();
    
    // Configurar identificación
    $dte->identificacion = new Identificacion();
    $dte->identificacion->numeroControl = "DTE-01-M001P001-0000". $paradte;  //DTE-01-F0000001-000080000000263
    $dte->identificacion->codigoGeneracion = getGUID(); //"7DEEF1AF-7DF7-436F-B9AE-47CA46035F1B";
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
    $dte->receptor->tipoDocumento = "37";
    $dte->receptor->numDocumento = $cliente[0]->dui;
    $dte->receptor->nrc = null;
    $dte->receptor->nombre = $cliente[0]->nombre;
    $dte->receptor->codActividad = "41001";
    $dte->receptor->descActividad = "Clientes Frecuentes";
    $dte->receptor->direccion = new Direccion();
    $dte->receptor->direccion->departamento = "02";
    $dte->receptor->direccion->municipio = "01";
    $dte->receptor->direccion->complemento = $cliente[0]->direccion;
    $dte->receptor->telefono = $cliente[0]->telefono;
    $dte->receptor->correo = $cliente[0]->correo;

$cuerpo = [];
$totalGravada = 0;
$itemnum = 1;
$totaliv = 0;
    foreach ($detalles as $detalle) {
   

    // Configurar cuerpo del documento
    $item = new ItemDocumento();
    $item->numItem = $itemnum;
    $itemnum += 1;
    $item->tipoItem = 1;
    $item->numeroDocumento = null;
    $item->cantidad = $detalle->cantidad;
    $item->codigo = "1";
    $item->codTributo = null;
    $item->uniMedida = 59;
    $item->descripcion = $detalle->descripcion;
    $item->precioUni = round($detalle->preciouni, 2);
    $item->montoDescu = 0;
    $item->ventaNoSuj = 0;
    $item->ventaExenta = 0;
    $item->ventaGravada = round($detalle->preciouni * $detalle->cantidad, 2);
    $totalGravada += $item->ventaGravada;
    $cuerpo[] = $item;
    $item->psv = $item->ventaGravada;
    $item->noGravado = 0;
    $item->ivaItem = round(($item->ventaGravada / 1.13) * 0.13, 2);  
    $totaliv += $item->ivaItem;
    $dte->cuerpoDocumento = [$item];
}
$dte->cuerpoDocumento = $cuerpo;


    // Configurar resumen
    $dte->resumen = new Resumen();
    $dte->resumen->totalNoSuj = 0.00;
    $dte->resumen->totalExenta = 0.00;
    $dte->resumen->totalGravada = round($totalGravada, 2);
   //dd(round(sacartotal($detalles), 2));
    $dte->resumen->descuNoSuj = 0.00;
    $dte->resumen->subTotalVentas = round($totalGravada, 2);
    $dte->resumen->descuExenta = 0.00;
    $dte->resumen->descuGravada = 0.00;
    $dte->resumen->porcentajeDescuento = 0.00;
    $dte->resumen->totalDescu = 0.00;
    $dte->resumen->subTotal = round($totalGravada, 2);
    $dte->resumen->ivaRete1 = 0.00;
    $dte->resumen->reteRenta = 0.00;
    $dte->resumen->montoTotalOperacion = round($totalGravada, 2);
    $dte->resumen->totalNoGravado = 0.00;
    $dte->resumen->totalPagar = round($totalGravada, 2);
    $total = round($totalGravada, 2);
    //$dte->resumen->totalLetras = " DÓLARES 00/100";
    $dte->resumen->totalLetras = numeroALetras($total);
    $dte->resumen->totalIva = round($totaliv, 2);

    $dte->resumen->saldoFavor = 0.00;
    $dte->resumen->condicionOperacion = 1;
   
    $dte->resumen->pagos = [
        [
            "codigo"=>"01",
            "montoPago"=>$totalGravada,
            "referencia"=>"0000",
            "periodo"=>null,
            "plazo"=>null
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
//$dte = crearDTE($fecha_actual, $cliente, $hora_actual, $detalles);
//echo "<pre>JSON generado:<br>" . json_encode($dte, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";



// Función para enviar DTE a la API



// Ejemplo de uso
// Iniciar proceso automáticamente al abrir el archivo desde el navegador
try {
    echo "Iniciando generación de DTE...<br>";
    $dte = crearDTE($fecha_actual, $cliente, $hora_actual, $detalles);
    echo "DTE generado correctamente.<br>";
 
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
    
 

// 5) Persistir en BD
ContingenciaDTE::create([
'tipo_dte' => $dteArray['identificacion']['tipoDte'] ?? null,
'numero_control' => $numControl,
'codigo_generacion' => $codigoGeneracion,
'json_original_path' => $rutaOriginal,
'estado' => 'pendiente',
'factura' => $detalles[0]->coticode ?? null,
'cliente' => $cliente[0]->id ?? null,
'fecha_generacion' => now(),
'total' => $dteArray['resumen']['montoTotalOperacion'] ?? 0.00,
'motivo' => 'Falla en el sistema de facturación',
'tipo_modelo' => 3 ,

]);

echo '
<p></p>

&nbsp; &nbsp; &nbsp;
<a href="/contingencia" class="btn btn-danger">Regresar </a>
';
//Termina Almacenar datos del DTE

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
///header("Refresh: 3; url=https://xanadusistema.com/facturacion");

?>
