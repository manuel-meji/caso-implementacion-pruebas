<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InvalidArgumentException;

class Grupo extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'abierto' => 'boolean',
        ];
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(Docente::class, 'grupo_docente', 'grupo_id', 'docente_id');
    }

    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(Estudiante::class, 'estudiantes_matriculados', 'grupo_id', 'estudiante_id')
            ->using(EstudianteMatriculado::class)
            ->withPivot('nota');
    }

    public function asignarDocente(Docente $d): void
    {
        if ($this->docentes()->count() >= 2) {
            throw new DomainException('Un grupo no puede tener más de 2 docentes.');
        }

        $this->docentes()->attach($d->id);
    }

    public function puedeAbrirse(): bool
    {
        return $this->docentes()->count() >= 1 && $this->estudiantes()->count() >= 15;
    }

    public function abrir(): void
    {
        if (! $this->puedeAbrirse()) {
            throw new DomainException('El grupo no cumple los requisitos para abrirse.');
        }

        $this->abierto = true;
        $this->save();
    }

    public function registrarNota(Estudiante $e, float $nota): void
    {
        if ($nota < 0 || $nota > 10) {
            throw new InvalidArgumentException('La nota debe estar entre 0 y 10.');
        }

        $this->estudiantes()->updateExistingPivot($e->id, ['nota' => $nota]);
    }
}
