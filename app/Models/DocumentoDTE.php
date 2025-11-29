<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class DocumentoDTE extends Model
{
protected $fillable = [
'sello_recibido','codigo_generacion','numero_control','factura','fecha_generacion','tipo_dte',
'json_original_path','json_legible_path','json_firmado_path','pdf_path', 'estado', 'motivo_anulacion', 'fecha_anulacion',
// Contingencia
'es_contingencia','tipo_contingencia','motivo_contingencia','fecha_contingencia',
'regularizado','fecha_regularizacion','error_regularizacion',
];


protected $casts = [
'es_contingencia' => 'boolean',
'regularizado' => 'boolean',
'fecha_contingencia' => 'datetime',
'fecha_regularizacion' => 'datetime',
];


public function getMontoTotal()
{
    try {
        if (!$this->json_legible_path || !Storage::exists($this->json_legible_path)) {
            return 0;
        }

        $json = json_decode(Storage::get($this->json_legible_path), true);

        return $json['resumen']['montoTotalOperacion'] ?? 0;
    } catch (\Throwable $e) {
        return 0;
    }
}
}