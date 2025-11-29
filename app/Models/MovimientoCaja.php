<?php

// app/Models/MovimientoCaja.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimientoCaja extends Model
{
    use HasFactory;
    protected $table = 'movimientos_caja'; // 👈 nombre correcto de la tabla

    protected $fillable = [
        'caja_id', 'tipo', 'descripcion', 'monto',
        'referencia_id', 'referencia_type', 'user_id'
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referencia()
    {
        return $this->morphTo();
    }
}