<?php

namespace App\Models;
use App\Models\Proveedor;
use App\Models\CompraDetalle;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = ['fecha', 'total', 'proveedor_id'];

public function proveedor()
{
    return $this->belongsTo(Proveedor::class);
}
    
    public function detalles()
{
    return $this->hasMany(CompraDetalle::class);
}
}
