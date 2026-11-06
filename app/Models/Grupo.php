<?php

namespace App\Models;

use Database\Factories\GrupoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Curso;

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

    public function asignarCurso(Curso $cursoAsignado) {
        $this->curso()->associate($cursoAsignado);
    }

    //No hay una tabla grupo_docente, pero si se guardan los docentes en la tabla grupos.
    public function asignarDocente(Docente $docenteAsignado) {
        if ($this->docente1 == null) {
            $this->docente1()->associate($docenteAsignado);
        } else if ($this->docente2 == null) {
            $this->docente2()->associate($docenteAsignado);
        }
    }

    public function puedeAbrirse(): bool{
        $cantidadEstudiantes = DB::table('estudiante_matriculados')
            ->where('grupo_id', $this->id)
            ->count();
        if ($cantidadEstudiantes >= 15 and $this->docente1 != null) {
            return true;
        }
        return false;
    }

    public function abrirGrupo(): bool{ {
        if ($this->puedeAbrirse()) {
            $this->abierto = true;
            $this->save();
            return true;
        }
        return false;
    }
    }

    
    

}
