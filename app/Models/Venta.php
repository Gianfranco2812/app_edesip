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

 
    protected $casts = [
        'fecha_venta' => 'datetime',
    ];



    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

  
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }


    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }


    public function contrato()
    {
        return $this->hasOne(Contrato::class, 'venta_id');
    }


    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'venta_id');
    }
    public function getEstadoActualAttribute()
    {
        if ($this->estado === 'Anulada') {
            return 'Anulada';
        }

        
        if ($this->contrato && ($this->contrato->estado === 'Firmado' || $this->contrato->ruta_pdf)) {
            return 'Cerrada';
        }

        return 'En Proceso';
    }
}
