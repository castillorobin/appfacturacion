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
// Ejemplo de DTE original a reportar


// Clases para estructurar el DTE
class Identificacion {
    public $version = 3;
    public $ambiente = "00";
    public $codigoGeneracion;
    public $fTransmision;
    public $hTransmision;
}


class Emisor {
    public $nit;
    public $nombre;
    public $nombreResponsable;
    public $tipoDocResponsable;
    public $numeroDocResponsable;
    public $tipoEstablecimiento;
    public $codEstableMH;
    public $codPuntoVenta;
    public $telefono;
    public $correo;
}

class detalleDTE {
    public $noItem;
    public $codigoGeneracion;
    public $tipoDoc;
  
}


class motivo {
    public $fInicio;
    public $fFin;
    public $hInicio;
    public $hFin;
    public $tipoContingencia;
    public $motivoContingencia;
}

class DocumentoTributarioElectronico {
    public $identificacion;  
    public $emisor;
    public $detalleDTE;
    public $motivo;
   
}
date_default_timezone_set('America/El_Salvador');
$fecha_actual = date("Y-m-d");
$hora_actual = date("h:i:s");

// Función para crear el DTE
function crearDTE($fecha_actual, $hora_actual, $original) {
    
    $dte = new DocumentoTributarioElectronico();
    
    // Configurar identificación
    $dte->identificacion = new Identificacion();
   // $dte->identificacion->numeroControl = "DTE-01-M001P001-0000". $paradte;  //DTE-01-F0000001-000080000000263
    $dte->identificacion->codigoGeneracion = getGUID(); //"7DEEF1AF-7DF7-436F-B9AE-47CA46035F1B";
    $dte->identificacion->fTransmision = $fecha_actual;
    $dte->identificacion->hTransmision = $hora_actual;

    // Configurar emisor
    $dte->emisor = new Emisor();
    $dte->emisor->nit = "008688551";
    $dte->emisor->nombre = "VILMA JANNET GODOY MENDOZA";
    $dte->emisor->nombreResponsable = "VILMA GODOY";
    $dte->emisor->tipoDocResponsable = "37";
    $dte->emisor->numeroDocResponsable = "0000001";
    $dte->emisor->tipoEstablecimiento = "02";
    $dte->emisor->codEstableMH = null;
    $dte->emisor->codPuntoVenta = null;
    $dte->emisor->telefono = "2429-0920";
    $dte->emisor->correo = "vilmademendoza71@gmail.com";
    
    
    
    // Configurar detalleDTE

    $dte->detalleDTE = new detalleDTE();
    $dte->detalleDTE = [
    [
        "noItem" => 1,
        "codigoGeneracion" => $original['identificacion']['codigoGeneracion'],
        "tipoDoc" => "03"
    ]
    ];
    $dte->motivo = new motivo();
    $dte->motivo->fInicio = $fecha_actual;
    $dte->motivo->fFin = $fecha_actual;
    $dte->motivo->hInicio = "05:00:00";
    $dte->motivo->hFin = date("H:i:s", strtotime('-5 minutes'));
    $dte->motivo->tipoContingencia = 3;
    $dte->motivo->motivoContingencia = "Falla en el sistema de facturación electrónica";

    return $dte;
    
}





// Función para enviar DTE a la API

function enviarDTEAPI($dte) {
    $datos = [
        'Usuario' => "02022504711049",
        'Password' => "Camioneta2025.",
        'Ambiente' => '00',
        'DteJson' => json_encode($dte),
        'Nit' => "008688551",
        'PasswordPrivado' => "Camioneta2025",
        
    ];

   // echo "<pre>JSON enviado a la API:<br>" . json_encode($datos, JSON_PRETTY_PRINT) . "</pre>";

    $ch = curl_init('http://98.89.90.33:7122/api/contingencia');
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
    $dte = crearDTE($fecha_actual, $hora_actual, $original);
    echo "DTE generado correctamente.<br>";
    echo "Iniciando transferencia a la API...<br>";
    $respuestaAPI = enviarDTEAPI($dte);
    echo "Respuesta recibida de la API.<br>";
    // Imprimir sello de recepción antes de enviar el correo
    if (isset($respuestaAPI->selloRecibido)) {
    echo "Sello de recepción: " . $respuestaAPI->selloRecibido . "<br>";
} elseif (isset($respuestaAPI->SelloRecepcion)) {
    echo "Sello de recepción (SelloRecepcion): " . $respuestaAPI->SelloRecepcion . "<br>";
} elseif (isset($dte->identificacion->codigoGeneracion)) {
    echo "Código de generación: " . $dte->identificacion->codigoGeneracion . "<br>";
}
//echo "<pre>JSON generado:<br>" . json_encode($dte, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    echo "Proceso completado exitosamente. <br>";

// Almacenar datos del DTE

// 4) Mostrar JSON generado



// 5) Persistir en BD
ContingenciaDTE::where('codigo_generacion', $original['identificacion']['codigoGeneracion'])->update([
    'estado' => 'reportado',
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
