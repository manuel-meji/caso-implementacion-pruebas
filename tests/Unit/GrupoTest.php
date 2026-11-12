<?php

use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Grupo;

test('la instancia de grupo contiene toda la informacion real asociada', function () {
    $docenteTitular = Docente::factory()->create([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'Carlos',
        'apellidosEmpleado' => 'Mora Solis',
    ]);

    $docenteSecundario = Docente::factory()->create([
        'codEmpleado' => 1002,
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Rojas Castro',
    ]);

    $docenteConsejero = Docente::factory()->create([
        'codEmpleado' => 1003,
        'nombreEmpleado' => 'Laura',
        'apellidosEmpleado' => 'Vargas Mena',
    ]);

    $curso = Curso::factory()->create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Analisis & Diseno de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = Grupo::factory()->create([
        'numeroCiclo' => 1,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => true,
        'curso_id' => $curso->id,
    ]);

    $grupo->asignarDocente($docenteTitular);
    $grupo->asignarDocente($docenteSecundario);

    $estudiante1 = Estudiante::factory()->create([
        'carne' => 'C4H071',
        'nombre' => 'Manuel',
        'apellidos' => 'Mejias Hernandez',
        'consejero_id' => $docenteConsejero->id,
    ]);

    $estudiante2 = Estudiante::factory()->create([
        'carne' => 'B91234',
        'nombre' => 'Sofia',
        'apellidos' => 'Gomez Perez',
        'consejero_id' => $docenteConsejero->id,
    ]);

    $grupo->estudiantes()->attach($estudiante1->id, ['nota' => 9.5]);
    $grupo->estudiantes()->attach($estudiante2->id, ['nota' => 8.8]);

    expect($grupo)->toBeInstanceOf(Grupo::class);
    expect($grupo->numeroCiclo)->toBe(1);
    expect($grupo->ano)->toBe(2026);
    expect($grupo->numeroGrupo)->toBe(1);
    expect($grupo->abierto)->toBeTrue();

    expect($grupo->curso)->toBeInstanceOf(Curso::class);
    expect($grupo->curso->sigla)->toBe('IF6100');
    expect($grupo->curso->nombreCurso)->toBe('Analisis & Diseno de Sistemas');
    expect($grupo->curso->creditos)->toBe(4);

    expect($grupo->docentes)->toHaveCount(2);
    expect($grupo->docentes->pluck('codEmpleado')->all())->toContain(1001, 1002);

    expect($grupo->estudiantes)->toHaveCount(2);
    $primero = $grupo->estudiantes->firstWhere('carne', 'C4H071');
    expect($primero->pivot->nota)->toBe(9.5);
    expect($primero->consejero->codEmpleado)->toBe(1003);
});
