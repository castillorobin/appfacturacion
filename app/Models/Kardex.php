<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    
    protected $table = 'kardexes';
    protected $fillable = [
    'producto_id',
    'fecha',
    'tipo',
    'documento',
    'descripcion',
    'Eunidad',
    'Ecosto',
    'Sunidad',
    'Scosto',
    'Tunidad',
    'Tcostop',
    'saldo',
    ];
 protected $casts = [
        'fecha' => 'datetime',
    ];

    public function producto()
    {
    return $this->belongsTo(Producto::class);
    }
}
