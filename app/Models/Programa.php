<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;
    protected $table = 'programas';

    protected $fillable = [
        'tipo_programa_id',
        'plantilla_contrato_id',
        'nombre',
        'descripcion_detallada',
        'horas_totales',
        'imagen_promocional_url',
        'brochure_pdf_url',
        'estado',
    ];


    public function tipoPrograma()
    {
        return $this->belongsTo(TipoPrograma::class, 'tipo_programa_id');
    }

    public function plantillaContrato()
    {
        return $this->belongsTo(PlantillaContrato::class, 'plantilla_contrato_id');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
