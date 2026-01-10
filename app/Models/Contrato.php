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
        'plantilla_contrato_id', // Cambiado de 'plantilla_id' para coincidir con la migración
        'token_acceso',
        'contenido_generado',
        'estado',
        'fecha_confirmacion',
        'ip_confirmacion',
        'ruta_pdf',
    ];

    /**
     * Campos que deben ser tratados como fechas.
     */
    protected $casts = [
        'fecha_confirmacion' => 'datetime',
    ];

    
    // --- RELACIONES ELOQUENT ---

    /**
     * Un Contrato PERTENECE A una Venta.
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    /**
     * Un Contrato PERTENECE A una Plantilla de Contrato.
     */
    public function plantilla()
    {
        return $this->belongsTo(PlantillaContrato::class, 'plantilla_contrato_id');
    }
}
