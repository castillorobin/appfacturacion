<?php

namespace App\Services;

use App\Models\DocumentoDTE;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
use DateTime;

class DTEService
{
    public function generarYEnviarDTE($cliente, $detalles)
    {
        date_default_timezone_set('America/El_Salvador');
        $fecha_actual = date("Y-m-d");
        $hora_actual = date("H:i:s");

        // Generar DTE
        $dte = $this->crearDTE($fecha_actual, $cliente, $hora_actual, $detalles);

        // Enviar a API
        $respuestaAPI = $this->enviarDTEAPI($dte, $cliente);

        // Procesar y guardar archivos
        $dteArray = json_decode(json_encode($dte), true);
        $codigoGeneracion = $respuestaAPI->codigoGeneracion ?? ($dteArray['identificacion']['codigoGeneracion'] ?? (string) Str::uuid());
        $numControl = $respuestaAPI->numControl ?? ($dteArray['identificacion']['numeroControl'] ?? null);
        $selloRecibido = $respuestaAPI->selloRecibido ?? null;
        $jwsFirmado = $respuestaAPI->dteFirmado ?? null;

        $rutaOriginal = "dtes_json/original_{$codigoGeneracion}.json";
        Storage::put($rutaOriginal, json_encode($dteArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $legible = $dteArray;
        $legible['identificacion']['codigoGeneracion'] = $codigoGeneracion;
        if ($numControl) {
            $legible['identificacion']['numeroControl'] = $numControl;
        }
        if ($jwsFirmado) {
            $legible['firmaElectronica'] = $jwsFirmado;
        }
        if ($selloRecibido) {
            unset($legible['selloRecibido']);
            $legible = array_merge($legible, ['selloRecibido' => $selloRecibido]);
        }

        $rutaLegible = "dtes_json/legible_{$codigoGeneracion}.json";
        Storage::put($rutaLegible, json_encode($legible, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $rutaFirmado = null;
        if ($jwsFirmado) {
            $rutaFirmado = "dtes_json/firmado_{$codigoGeneracion}.json";
            Storage::put($rutaFirmado, $jwsFirmado);
        }

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
        ]);

        return [
            'codigo_generacion' => $codigoGeneracion,
            'numero_control' => $numControl,
            'sello_recibido' => $selloRecibido
        ];
    }

    private function crearDTE($fecha_actual, $cliente, $hora_actual, $detalles)
    {
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
    $dte->emisor->nit = "05090211591010";
    $dte->emisor->nrc = "1834284";
    $dte->emisor->nombre = "Santos Guerrero";
    $dte->emisor->codActividad = "55101";
    $dte->emisor->descActividad = "ALOJAMIENTO PARA ESTANCIAS CORTAS";
    $dte->emisor->nombreComercial = "AUTOMOTEL XANADU";
    $dte->emisor->tipoEstablecimiento = "02";
    $dte->emisor->direccion = new Direccion();
    $dte->emisor->direccion->departamento = "02";
    $dte->emisor->direccion->municipio = "01";
    $dte->emisor->direccion->complemento = "Carretera a los naranjos, Lotificacion San Fernando #3 Poligono B";
    $dte->emisor->telefono = "2429-0920";
    $dte->emisor->codEstableMH = null;
    $dte->emisor->codEstable = null;
    $dte->emisor->codPuntoVentaMH = null;
    $dte->emisor->codPuntoVenta = null;
    $dte->emisor->correo = "clientesfrecuentes01@gmail.com";

    // Configurar receptor
    $dte->receptor = new Receptor();
    $dte->receptor->tipoDocumento = "37";
    $dte->receptor->numDocumento = $cliente[0]->DUI;
    $dte->receptor->nrc = null;
    $dte->receptor->nombre = $cliente[0]->Nombre;
    $dte->receptor->codActividad = "41001";
    $dte->receptor->descActividad = "Clientes Frecuentes";
    $dte->receptor->direccion = new Direccion();
    $dte->receptor->direccion->departamento = "02";
    $dte->receptor->direccion->municipio = "01";
    $dte->receptor->direccion->complemento = $cliente[0]->Direccion;
    $dte->receptor->telefono = $cliente[0]->Telefono;
    $dte->receptor->correo = $cliente[0]->Correo;

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

    private function enviarDTEAPI($dte, $cliente)
    {
        $datos = [
            'Usuario' => "05090211591010",
            'Password' => "Santos25.",
            'Ambiente' => '00',
            'DteJson' => json_encode($dte),
            'Nit' => "005207550",
            'PasswordPrivado' => "25Xanadu20.",
            'TipoDte' => '01',
            'CodigoGeneracion' => $dte->identificacion->codigoGeneracion,
            'NumControl' => $dte->identificacion->numeroControl,
            'VersionDte' => 1,
            'CorreoCliente' => $cliente[0]->Correo
        ];

        $ch = curl_init('http://34.198.24.200:7122/api/procesar-dte');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Error cURL: $error");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200) {
            throw new Exception("Error al procesar DTE: $response (HTTP $httpCode)");
        }

        return json_decode($response);
    }
}
