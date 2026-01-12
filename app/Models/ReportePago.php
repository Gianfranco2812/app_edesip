<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportePago extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'cuota_id',      
        'monto',
        'metodo_pago',
        'numero_operacion',
        'comprobante_imagen',
        'estado',
        'observacion_admin'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
    
    public function cuota()
    {
        return $this->belongsTo(Cuota::class);
    }
}
