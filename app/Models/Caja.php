<?php

// app/Models/Caja.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'fecha_apertura', 'fecha_cierre',
        'monto_inicial', 'monto_final', 'estado'
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usuario()
{
    return $this->belongsTo(User::class, 'user_id');
}
}