<?php

use App\Models\Estudiante;
use App\Models\EstudianteMatriculado;
use App\Models\Grupo;
use Illuminate\Database\QueryException;

mutates(Grupo::class);

test('CP-07 Un grupo sin docente no se abre aunque tenga cupo completo', function () {
    $curso = crearCursoIF6100();
    $grupo = crearGrupo01($curso);

    $estudiantes = Estudiante::factory()->count(20)->create();
    foreach ($estudiantes as $estudiante) {
        $grupo->estudiantes()->attach($estudiante->id);
    }

    expect($grupo->puedeAbrirse())->toBeFalse();
    expect(fn () => $grupo->abrir())->toThrow(DomainException::class);
    expect($grupo->fresh()->abierto)->toBeFalse();
});

test('CP-08 Un estudiante queda ligado a su profesor consejero', function () {
    $docente = crearDocente1001();
    $estudiante = crearEstudianteC12345($docente);

    expect($estudiante->consejero->codEmpleado)->toBe(1001);
    expect($docente->estudiantes)->toHaveCount(1);
    expect($docente->estudiantes->first()->carne)->toBe('C12345');
    expect($estudiante->consejero_id)->toBe($docente->id);
});

test('CP-09 Cambiar de consejero reemplaza al anterior, no lo suma', function () {
    $docente1 = crearDocente1001();
    $docente2 = crearDocente1002();
    $estudiante = crearEstudianteC12345($docente1);

    $estudiante->consejero()->associate($docente2);
    $estudiante->save();

    $docente1 = $docente1->fresh();
    $docente2 = $docente2->fresh();

    expect($estudiante->fresh()->consejero->codEmpleado)->toBe(1002);
    expect($docente1->estudiantes)->toHaveCount(0);
    expect($docente2->estudiantes)->toHaveCount(1);
    expect($estudiante->fresh()->consejero_id)->toBe($docente2->id);
});

test('CP-10 Al matricular, la nota queda vacía', function () {
    $curso = crearCursoIF6100();
    $grupo = crearGrupo01($curso);
    $docente = crearDocente1001();
    $estudiante = crearEstudianteC12345($docente);

    $grupo->estudiantes()->attach($estudiante->id);

    $pivote = $grupo->estudiantes->first()->pivot;

    expect($pivote)->toBeInstanceOf(EstudianteMatriculado::class);
    expect($pivote->nota)->toBeNull();
    $this->assertDatabaseHas('estudiantes_matriculados', [
        'grupo_id' => $grupo->id,
        'estudiante_id' => $estudiante->id,
    ]);
    $this->assertDatabaseCount('estudiantes_matriculados', 1);
});

test('CP-11 Registrar la nota al finalizar el curso', function () {
    $curso = crearCursoIF6100();
    $grupo = crearGrupo01($curso);
    $docente = crearDocente1001();
    $estudiante = crearEstudianteC12345($docente);

    $grupo->estudiantes()->attach($estudiante->id);
    $grupo->registrarNota($estudiante, 8.5);

    $matricula = EstudianteMatriculado::where('grupo_id', $grupo->id)
        ->where('estudiante_id', $estudiante->id)
        ->first();

    expect($matricula->nota)->toBeFloat();
    expect($matricula->nota)->toBe(8.5);
    expect($matricula->grupo_id)->toBe($grupo->id);
    expect($matricula->estudiante_id)->toBe($estudiante->id);
});

test('CP-12 Rechazar notas menores que 0 o mayores que 10', function (float $nota) {
    $curso = crearCursoIF6100();
    $grupo = crearGrupo01($curso);
    $docente = crearDocente1001();
    $estudiante = crearEstudianteC12345($docente);

    $grupo->estudiantes()->attach($estudiante->id);

    expect(fn () => $grupo->registrarNota($estudiante, $nota))->toThrow(InvalidArgumentException::class);

    $matricula = EstudianteMatriculado::where('grupo_id', $grupo->id)
        ->where('estudiante_id', $estudiante->id)
        ->first();

    expect($matricula->nota)->toBeNull();
})->with([-1, 10.5]);

test('CP-13 Un estudiante no se matricula dos veces en el mismo grupo', function () {
    $curso = crearCursoIF6100();
    $grupo = crearGrupo01($curso);
    $docente = crearDocente1001();
    $estudiante = crearEstudianteC12345($docente);

    $grupo->estudiantes()->attach($estudiante->id);

    expect(fn () => $grupo->estudiantes()->attach($estudiante->id))->toThrow(QueryException::class);

    $cantidad = EstudianteMatriculado::where('grupo_id', $grupo->id)
        ->where('estudiante_id', $estudiante->id)
        ->count();

    expect($cantidad)->toBe(1);
});

test('C3 Aceptar notas en los límites exactos 0 y 10', function (float $notaLimite) {
    $curso = crearCursoIF6100();
    $grupo = crearGrupo01($curso);
    $docente = crearDocente1001();
    $estudiante = crearEstudianteC12345($docente);

    $grupo->estudiantes()->attach($estudiante->id);
    $grupo->registrarNota($estudiante, $notaLimite);

    $matricula = EstudianteMatriculado::where('grupo_id', $grupo->id)
        ->where('estudiante_id', $estudiante->id)
        ->first();

    expect($matricula->nota)->toBe($notaLimite);
})->with([0.0, 10.0]);
