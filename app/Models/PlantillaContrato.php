<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaContrato extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si es el plural del modelo)
    protected $table = 'plantillas_contrato';

    // AÑADE ESTA LÍNEA
    protected $fillable = ['nombre_plantilla', 'contenido'];

    // Define la relación (opcional pero recomendado)
    public function programas()
    {
        return $this->hasMany(Programa::class);
    }
}
