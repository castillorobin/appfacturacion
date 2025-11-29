<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<?php
use App\Models\DocumentoDTE;
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




// Función para enviar DTE a la API

function enviarDTEAPI($original) {
    $datos = [
        'Usuario' => "02022504711049",
        'Password' => "Camioneta2025.",
        'Ambiente' => '00',
        'DteJson' => json_encode($original),
        'Nit' => "008688551",
        'PasswordPrivado' => "Camioneta2025",
        'TipoDte' => '03',
        'CodigoGeneracion' => $original['identificacion']['codigoGeneracion'],
        'NumControl' => $original['identificacion']['numeroControl'],
        'VersionDte' => 3,
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
    //echo "Iniciando generación de DTE...<br>";
    //$dte = crearDTE($fecha_actual, $cliente, $hora_actual, $detalles);
    //echo "DTE generado correctamente.<br>";
    echo "Iniciando transferencia a la API...<br>";
    $respuestaAPI = enviarDTEAPI($original);
    echo "Respuesta recibida de la API.<br>";
    // Imprimir sello de recepción antes de enviar el correo
    if (isset($respuestaAPI->selloRecibido)) {
    echo "Sello de recepción: " . $respuestaAPI->selloRecibido . "<br>";
} elseif (isset($respuestaAPI->SelloRecepcion)) {
    echo "Sello de recepción (SelloRecepcion): " . $respuestaAPI->SelloRecepcion . "<br>";
} elseif (isset($original->identificacion->codigoGeneracion)) {
    echo "Código de generación: " . $original->identificacion->codigoGeneracion . "<br>";
}
    echo "Proceso completado exitosamente.<br>";

     // Almacenar datos del DTE
$dteArray = json_decode(json_encode($original), true);
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

ContingenciaDTE::where('codigo_generacion', $original['identificacion']['codigoGeneracion'])->update([
    'estado' => 'enviado',
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
