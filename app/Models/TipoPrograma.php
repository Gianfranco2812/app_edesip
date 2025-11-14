<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPrograma extends Model
{
    use HasFactory;

    protected $table = 'tipos_programa';
    protected $fillable = ['nombre'];

    public function programas()
    {
        return $this->hasMany(Programa::class);
    }
}
