<?php

namespace App\Models;

use Database\Factories\DocenteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    /** @use HasFactory<DocenteFactory> */
    use HasFactory;

    protected $fillable = [
        'codEmpleado',
        'nombreEmpleado',
        'apellidosEmpleado',
    ];

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'docente_id');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'docente1_id');
    }

    public function gruposSecundarios()
    {
        return $this->hasMany(Grupo::class, 'docente2_id');
    }
}
