<?php

namespace App\Models;

use Database\Factories\EstudianteMatriculadoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteMatriculado extends Model
{
    /** @use HasFactory<EstudianteMatriculadoFactory> */
    use HasFactory;

    protected $table = 'estudiante_matriculados';

    protected $fillable = [
        'estudiante_id',
        'grupo_id',
        'nota',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }
}
