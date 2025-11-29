<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContadorDTE extends Model
{
    protected $table = 'contadores_dte';

    protected $fillable = [
        'tipo_dte',
        'numero_actual',
    ];
}
