<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{

    protected $table = 'proveedores';
    protected $fillable = [
    'nombre',
    'direccion',
    'tipo_persona',
    'tipo_contribuyente',
    'pais',
    'departamento',
    'municipio',
    'telefono',
    'giro',
    'dui',
    'nit',
];

}
