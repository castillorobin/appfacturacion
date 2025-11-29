<?php

namespace App\Models\Models;
use App\Models\Actividad;
use App\Models\Departamento;
use App\Models\Municipio;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
              'nombre',
    'nombre_comercial',
    'nit',
    'dui',
    'nrc',
    'telefono',
    'direccion',
    'correo',
    'actividad_economica_id',
    'departamento_id',
    'municipio_id',
    ];
public function actividadEconomica()
{
    return $this->belongsTo(Actividad::class, 'actividad_economica_id');
}

public function departamento()
{
    return $this->belongsTo(Departamento::class, 'departamento_id');
}

public function municipio()
{
    return $this->belongsTo(Municipio::class, 'municipio_id');
}



}
