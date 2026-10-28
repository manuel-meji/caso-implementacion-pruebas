<?php

namespace App\Models;

use Database\Factories\GrupoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    /** @use HasFactory<GrupoFactory> */
    use HasFactory;

    protected $fillable = [
        'numeroCiclo',
        'ano',
        'numeroGrupo',
        'abierto',
        'curso_id',
        'docente1_id',
        'docente2_id',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function docente1()
    {
        return $this->belongsTo(Docente::class, 'docente1_id');
    }

    public function docente2()
    {
        return $this->belongsTo(Docente::class, 'docente2_id');
    }

    public function estudianteMatriculados()
    {
        return $this->hasMany(EstudianteMatriculado::class, 'grupo_id');
    }

    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_matriculados', 'grupo_id', 'estudiante_id');
    }
}
