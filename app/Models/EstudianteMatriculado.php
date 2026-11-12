<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EstudianteMatriculado extends Pivot
{
    use HasFactory;

    protected $table = 'estudiantes_matriculados';

    public $incrementing = true;

    protected $fillable = [
        'estudiante_id',
        'grupo_id',
        'nota',
    ];

    protected $casts = [
        'nota' => 'float',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }
}
