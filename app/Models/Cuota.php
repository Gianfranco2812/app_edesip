<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    use HasFactory;

    protected $table = 'cuotas';

    protected $fillable = [
        'venta_id',
        'descripcion',
        'monto_cuota',
        'fecha_vencimiento',
        'estado_cuota',
        'fecha_pago',
        'metodo_pago',
        'transaccion_id',
    ];

    /**
     * Campos que deben ser tratados como fechas.
     */
    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'datetime',
    ];

    
    // --- RELACIONES ELOQUENT ---

    /**
     * Una Cuota PERTENECE A una Venta.
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
    
    public function reportes()
    {
        return $this->hasMany(ReportePago::class, 'cuota_id');
    }

    // Helper: Para saber rápido si tiene algo pendiente
    public function getTieneReportePendienteAttribute()
    {
        // Retorna el primer reporte que esté en estado 'Pendiente'
        return $this->reportes->where('estado', 'Pendiente')->first();
    }
    public function pagos()
    {
        // 'cuota_id' es la llave foránea en tu tabla 'reporte_pagos'
        return $this->hasMany(ReportePago::class, 'cuota_id');
    }
}
