<?php

use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\EstudianteMatriculado;
use App\Models\Grupo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('la instancia de grupo contiene toda la informacion real asociada', function () {
    // 1. Docentes: titular, secundario y consejero
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

    // 2. Curso al que pertenece el grupo
    $curso = Curso::factory()->create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Analisis & Diseno de Sistemas',
        'creditos' => 4,
    ]);

    // 3. Instancia del grupo con toda su informacion asociada
    $grupo = Grupo::factory()->create([
        'numeroCiclo' => 1,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => true,
        'curso_id' => $curso->id,
        'docente1_id' => $docenteTitular->id,
        'docente2_id' => $docenteSecundario->id,
    ]);

    // 4. Estudiantes matriculados con su docente consejero y su nota final
    $estudiante1 = Estudiante::factory()->create([
        'carne' => 'C4H071',
        'nombre' => 'Manuel',
        'apellidos' => 'Mejias Hernandez',
        'docente_id' => $docenteConsejero->id,
    ]);

    $estudiante2 = Estudiante::factory()->create([
        'carne' => 'B91234',
        'nombre' => 'Sofia',
        'apellidos' => 'Gomez Perez',
        'docente_id' => $docenteConsejero->id,
    ]);

    $matricula1 = EstudianteMatriculado::factory()->create([
        'grupo_id' => $grupo->id,
        'estudiante_id' => $estudiante1->id,
        'nota' => 95.5,
    ]);

    $matricula2 = EstudianteMatriculado::factory()->create([
        'grupo_id' => $grupo->id,
        'estudiante_id' => $estudiante2->id,
        'nota' => 88.0,
    ]);

    // 5. Validacion de la instancia de Grupo y sus atributos
    expect($grupo)->toBeInstanceOf(Grupo::class);
    expect($grupo->numeroCiclo)->toBe(1);
    expect($grupo->ano)->toBe(2026);
    expect($grupo->numeroGrupo)->toBe(1);
    expect((bool) $grupo->abierto)->toBeTrue();

    // 6. Validacion del Curso asociado
    expect($grupo->curso)->toBeInstanceOf(Curso::class);
    expect($grupo->curso->sigla)->toBe('IF6100');
    expect($grupo->curso->nombreCurso)->toBe('Analisis & Diseno de Sistemas');
    expect($grupo->curso->creditos)->toBe(4);

    // 7. Validacion de los Docentes asociados
    expect($grupo->docente1)->toBeInstanceOf(Docente::class);
    expect($grupo->docente1->codEmpleado)->toBe(1001);
    expect($grupo->docente2)->toBeInstanceOf(Docente::class);
    expect($grupo->docente2->codEmpleado)->toBe(1002);

    // 8. Validacion de los estudiantes matriculados y sus notas
    $matriculas = $grupo->estudianteMatriculados;
    expect($matriculas)->toHaveCount(2);

    $primeraMatricula = $matriculas->firstWhere('estudiante_id', $estudiante1->id);
    expect($primeraMatricula)->not->toBeNull();
    expect((float) $primeraMatricula->nota)->toBe(95.5);
    expect($primeraMatricula->estudiante->carne)->toBe('C4H071');
    expect($primeraMatricula->estudiante->consejero->codEmpleado)->toBe(1003);

    $segundaMatricula = $matriculas->firstWhere('estudiante_id', $estudiante2->id);
    expect($segundaMatricula)->not->toBeNull();
    expect((float) $segundaMatricula->nota)->toBe(88.0);
    expect($segundaMatricula->estudiante->carne)->toBe('B91234');
});
