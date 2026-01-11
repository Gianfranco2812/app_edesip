<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'cliente_id',
        'grupo_id',
        'vendedor_id',
        'fecha_venta',
        'estado',
        'costo_total_venta',
        'costo_matricula_venta',
        'nro_cuotas_venta',
        'texto_promocional_venta',
    ];

    /**
     * Campos que deben ser tratados como fechas.
     */
    protected $casts = [
        'fecha_venta' => 'datetime',
    ];


    // --- RELACIONES ELOQUENT ---

    /**
     * Una Venta PERTENECE A un Cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Una Venta PERTENECE A un Grupo.
     */
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    /**
     * Una Venta PERTENECE A un Vendedor (Usuario).
     */
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    /**
     * Una Venta TIENE UN Contrato asociado.
     */
    public function contrato()
    {
        return $this->hasOne(Contrato::class, 'venta_id');
    }

    /**
     * Una Venta TIENE MUCHAS Cuotas (plan de pagos).
     */
    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'venta_id');
    }
    public function getEstadoActualAttribute()
    {
        // 1. Si la venta fue anulada manualmente, ese es su estado definitivo.
        if ($this->estado === 'Anulada') {
            return 'Anulada';
        }

        // 2. Si tiene contrato y ya tiene PDF (firmado) o estado 'Firmado'
        // Ajusta la condición según cómo guardes el contrato firmado
        if ($this->contrato && ($this->contrato->estado === 'Firmado' || $this->contrato->ruta_pdf)) {
            return 'Cerrada';
        }

        // 3. Si no está anulada ni firmada, sigue en proceso
        return 'En Proceso';
    }
}
