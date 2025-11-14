<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;
    // Nombre de la tabla
    protected $table = 'grupos';

    // Campos que se pueden llenar masivamente
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

    // Para que Eloquent trate estos campos como objetos Carbon (fechas)
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_termino' => 'date',
    ];

    // --- RELACIONES ELOQUENT ---

    // Un Grupo PERTENECE A un Programa (Relación Inversa)
    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }

    // (Más adelante) Un Grupo TIENE MUCHAS Ventas/Inscripciones
    // public function ventas()
    // {
    //     return $this->hasMany(Venta::class);
    // }
}
