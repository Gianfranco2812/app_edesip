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

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'datetime',
    ];

    

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
    
    public function reportes()
    {
        return $this->hasMany(ReportePago::class, 'cuota_id');
    }

    public function getTieneReportePendienteAttribute()
    {
        return $this->reportes->where('estado', 'Pendiente')->first();
    }
    public function pagos()
    {
        return $this->hasMany(ReportePago::class, 'cuota_id');
    }
}
