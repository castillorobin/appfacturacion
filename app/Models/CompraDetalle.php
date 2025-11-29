<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    protected $fillable = [
    'compra_id',
    'producto_id',
    'cantidad',
    'precio_unitario',
    'subtotal',
];

    public function producto()
{
    return $this->belongsTo(Producto::class);
}

public function compra()
{
    return $this->belongsTo(Compra::class);
}
}
