<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $table = 'contratos';

    protected $fillable = [
        'venta_id',
        'plantilla_contrato_id', 
        'token_acceso',
        'contenido_generado',
        'estado',
        'fecha_confirmacion',
        'ip_confirmacion',
        'ruta_pdf',
    ];


    protected $casts = [
        'fecha_confirmacion' => 'datetime',
    ];

    

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }


    public function plantilla()
    {
        return $this->belongsTo(PlantillaContrato::class, 'plantilla_contrato_id');
    }
}
