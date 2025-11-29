<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'unidad',
        'categoria_id',
        'precio_costo',
        'precio_venta',
        'stock',
    ];

    public function categoria()
{
    return $this->belongsTo(Categoria::class);
}
}