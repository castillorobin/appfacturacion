<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AjusteInventario extends Model
{
    protected $fillable = ['producto_id', 'tipo', 'cantidad', 'motivo'];

public function producto()
{
    return $this->belongsTo(Producto::class);
}
}
