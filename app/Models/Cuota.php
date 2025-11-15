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
}
