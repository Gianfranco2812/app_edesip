<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;
    // Define la tabla (por si acaso)
    protected $table = 'programas';

    // Campos que se pueden llenar masivamente
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

    // --- RELACIONES ELOQUENT ---

    // Un Programa PERTENECE A un Tipo (Relación Inversa)
    public function tipoPrograma()
    {
        return $this->belongsTo(TipoPrograma::class, 'tipo_programa_id');
    }

    // Un Programa PERTENECE A una Plantilla (Relación Inversa)
    public function plantillaContrato()
    {
        return $this->belongsTo(PlantillaContrato::class, 'plantilla_contrato_id');
    }

    // Un Programa TIENE MUCHOS Grupos
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
