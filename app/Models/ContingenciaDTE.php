<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContingenciaDTE extends Model
{
    protected $table = 'contingencia_d_t_e_s';

    protected $fillable = [
        'tipo_dte',
        'numero_control',
        'codigo_generacion',
        'json_original_path',
        'estado',
        'fecha_generacion',
        'cliente',
        'total',
        'motivo',
        'factura',
        'tipo_modelo'
    ];
}
