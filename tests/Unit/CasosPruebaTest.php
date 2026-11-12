<?php

use App\Models\Estudiante;
use App\Models\Grupo;

mutates(Grupo::class);

test('CP-01 Registrar un grupo con toda su información y su curso', function () {
    $curso = crearCursoIF6100();

    $grupo = new Grupo([
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => true,
    ]);
    $grupo->curso()->associate($curso);
    $grupo->save();

    $grupoRecuperado = Grupo::find($grupo->id);

    expect($grupoRecuperado->numeroCiclo)->toBe(2);
    expect($grupoRecuperado->ano)->toBe(2026);
    expect($grupoRecuperado->numeroGrupo)->toBe(1);
    expect($grupoRecuperado->abierto)->toBeBool();
    expect($grupoRecuperado->abierto)->toBeTrue();
    expect($grupoRecuperado->curso->sigla)->toBe('IF6100');
    expect($grupoRecuperado->curso->creditos)->toBe(4);
    expect(Grupo::count())->toBe(1);
});

test('CP-02 Un curso tiene varios grupos y se distinguen los abiertos', function () {
    $curso = crearCursoIF6100();

    $grupo1 = Grupo::factory()->create([
        'curso_id' => $curso->id,
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => true,
    ]);

    $grupo2 = Grupo::factory()->create([
        'curso_id' => $curso->id,
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 2,
        'abierto' => true,
    ]);

    $grupo3 = Grupo::factory()->create([
        'curso_id' => $curso->id,
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 3,
        'abierto' => false,
    ]);

    $grupos = $curso->grupos;
    $abiertos = $grupos->where('abierto', true);

    expect($grupos)->toHaveCount(3);
    expect($abiertos)->toHaveCount(2);
    expect($abiertos->pluck('numeroGrupo')->all())->toBe([1, 2]);
    expect($grupo1->curso_id)->toBe($curso->id);
    expect($grupo2->curso_id)->toBe($curso->id);
    expect($grupo3->curso_id)->toBe($curso->id);
});

test('CP-03 Un grupo acepta dos docentes', function () {
    $curso = crearCursoIF6100();
    $grupo = crearGrupo01($curso);
    $docente1 = crearDocente1001();
    $docente2 = crearDocente1002();

    $grupo->asignarDocente($docente1);
    $grupo->asignarDocente($docente2);

    expect($grupo->docentes)->toHaveCount(2);
    expect($grupo->docentes->pluck('codEmpleado')->all())->toBe([1001, 1002]);
    expect($docente1->grupos()->first()->id)->toBe($grupo->id);
    $this->assertDatabaseCount('grupo_docente', 2);
});

test('CP-04 Un grupo rechaza un tercer docente', function () {
    $curso = crearCursoIF6100();
    $grupo = crearGrupo01($curso);
    $docente1 = crearDocente1001();
    $docente2 = crearDocente1002();
    $docente3 = crearDocente1003();

    $grupo->asignarDocente($docente1);
    $grupo->asignarDocente($docente2);

    expect(fn () => $grupo->asignarDocente($docente3))->toThrow(DomainException::class);

    expect($grupo->docentes()->count())->toBe(2);
    $this->assertDatabaseMissing('grupo_docente', [
        'grupo_id' => $grupo->id,
        'docente_id' => $docente3->id,
    ]);
});

test('CP-05 Un grupo con 14 estudiantes no puede abrirse', function () {
    $curso = crearCursoIF6100();
    $grupo = crearGrupo01($curso);
    $docente1 = crearDocente1001();
    $grupo->asignarDocente($docente1);

    $estudiantes = Estudiante::factory()->count(14)->create();
    foreach ($estudiantes as $estudiante) {
        $grupo->estudiantes()->attach($estudiante->id);
    }

    expect($grupo->puedeAbrirse())->toBeFalse();
    expect(fn () => $grupo->abrir())->toThrow(DomainException::class);
    expect($grupo->fresh()->abierto)->toBeFalse();
});

test('CP-06 Un grupo con 15 estudiantes y un docente se abre', function () {
    $curso = crearCursoIF6100();
    $grupo = crearGrupo01($curso);
    $docente1 = crearDocente1001();
    $grupo->asignarDocente($docente1);

    $estudiantes = Estudiante::factory()->count(15)->create();
    foreach ($estudiantes as $estudiante) {
        $grupo->estudiantes()->attach($estudiante->id);
    }

    expect($grupo->puedeAbrirse())->toBeTrue();
    $grupo->abrir();
    expect($grupo->fresh()->abierto)->toBeTrue();
});
