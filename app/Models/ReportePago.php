<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportePago extends Model
{
    use HasFactory;

    // ESTA LÍNEA ES LA IMPORTANTE. Si falta algún campo aquí, dará Error 500.
    protected $fillable = [
        'venta_id',
        'cuota_id',       // <--- ¿Agregaste este?
        'monto',
        'metodo_pago',
        'numero_operacion',
        'comprobante_imagen',
        'estado',
        'observacion_admin'
    ];

    // Relaciones (opcional, pero buena práctica tenerlas)
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
    
    public function cuota()
    {
        return $this->belongsTo(Cuota::class);
    }
}
