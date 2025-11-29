<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConteoDTE extends Model
{
    protected $table = 'conteo_d_t_e_s';

    protected $fillable = [
        'tipo',
        'conteo',
    ];
}

