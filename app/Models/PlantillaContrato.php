<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaContrato extends Model
{
    use HasFactory;


    protected $table = 'plantillas_contrato';


    protected $fillable = ['nombre_plantilla', 'contenido'];


    public function programas()
    {
        return $this->hasMany(Programa::class);
    }
}
