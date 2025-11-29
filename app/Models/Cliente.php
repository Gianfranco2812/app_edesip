<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'tipo_documento',
        'numero_documento',
        'direccion',
        'fecha_nacimiento',
        'estado',
        'creado_por_vendedor_id',
        'user_id'
    ];

    
    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    // --- RELACIONES ELOQUENT ---

    // Un Cliente PERTENECE A un Vendedor/Asesor (Usuario)
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'creado_por_vendedor_id');
    }

    // Un Cliente TIENE MUCHAS Ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    // Un Cliente TIENE UN Nombre Completo (Atributo Mágico)
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
