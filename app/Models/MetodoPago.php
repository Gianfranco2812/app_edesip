<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    use HasFactory;
    protected $table = 'metodo_pagos';

    protected $fillable = [
        'nombre_banco',
        'tipo',
        'numero_cuenta',
        'cci',
        'titular',
        'qr_imagen',
        'instrucciones',
        'estado'
    ];
}
