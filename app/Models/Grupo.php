<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;
    protected $table = 'grupos';

    protected $fillable = [
        'programa_id',
        'codigo_grupo',
        'fecha_inicio',
        'fecha_termino',
        'modalidad',
        'horario_texto',
        'estado',
        'costo_total',
        'costo_matricula',
        'numero_cuotas',
        'texto_promocional',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_termino' => 'date',
    ];


    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }

}
