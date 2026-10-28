<?php

namespace App\Models;

use Database\Factories\EstudianteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    /** @use HasFactory<EstudianteFactory> */
    use HasFactory;

    protected $fillable = [
        'carne',
        'nombre',
        'apellidos',
        'docente_id',
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function consejero()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function estudianteMatriculados()
    {
        return $this->hasMany(EstudianteMatriculado::class, 'estudiante_id');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'estudiante_matriculados', 'estudiante_id', 'grupo_id');
    }
}
