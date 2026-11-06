<?php

use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\EstudianteMatriculado;
use App\Models\Grupo;

test('Registrar un grupo con toda su información y su curso', function () {

    $curso = new Curso()::factory()->create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = new Grupo()::factory()->create([
        'numeroGrupo' => '1',
        'ano' => 2026,
        'numeroCiclo' => 2,
        'abierto' => true
    ]);

    $grupo->asignarCurso($curso);
    
    expect($grupo->curso->sigla)->toEqual('IF6100');
    expect($grupo->numeroCiclo)->toEqual(2);
    expect($grupo->ano)->toEqual(2026);
    expect($grupo->abierto)->toEqual(true);

});



test('Un curso tiene varios grupos y se distinguen los abiertos', function () {
    $curso = new Curso()::factory()->create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo1 = new Grupo()::factory()->create([
        'numeroGrupo' => '1',
        'ano' => 2026,
        'numeroCiclo' => 2,
        'abierto' => true,
        'curso_id'=> $curso->id
    ]);

    $grupo2 = new Grupo()::factory()->create([
        'numeroGrupo' => '2',
        'ano' => 2026,
        'numeroCiclo' => 2,
        'abierto' => true,
        'curso_id'=> $curso->id
    ]);

    $grupo3 = new Grupo()::factory()->create([
        'numeroGrupo' => '3',
        'ano' => 2026,
        'numeroCiclo' => 2,
        'abierto' => false,
        'curso_id'=> $curso->id
    ]);

    $gruposAbiertos = $curso->listarGruposAbiertos();

    expect($gruposAbiertos)->toHaveCount(2);
});

test('Un grupo acepta dos docentes', function () {

    $curso = new Curso()::factory()->create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = new Grupo()::factory()->create([
        'numeroGrupo' => '01',
        'ano' => 2026,
        'numeroCiclo' => 2,
        'abierto' => true,
        'curso_id'=> $curso->id,
        'docente1_id'=>null,
        'docente2_id'=>null
    ]);

    $docente1 = new Docente()::factory()->create([
        'codEmpleado' => '1001',
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    $docente2 = new Docente()::factory()->create([
        'codEmpleado' => '1002',
        'nombreEmpleado' => 'Luis',
        'apellidosEmpleado' => 'Rojas Solano', 
    ]);


    $grupo->asignarDocente($docente1);
    $grupo->asignarDocente($docente2);

    expect($grupo->docente1->codEmpleado)->toEqual($docente1->codEmpleado);
    expect($grupo->docente2->codEmpleado)->toEqual($docente2->codEmpleado);

});


test('Un grupo rechaza un tercer docente', function (){
    $curso = new Curso()::factory()->create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = new Grupo()::factory()->create([
        'numeroGrupo' => '01',
        'ano' => 2026,
        'numeroCiclo' => 2,
        'abierto' => true,
        'curso_id'=> $curso->id,
        'docente1_id'=>null,
        'docente2_id'=>null
    ]);

    $docente1 = new Docente()::factory()->create([
        'codEmpleado' => '1001',
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    $docente2 = new Docente()::factory()->create([
        'codEmpleado' => '1002',
        'nombreEmpleado' => 'Luis',
        'apellidosEmpleado' => 'Rojas Solano', 
    ]);

    $docente3 = new Docente()::factory()->create([
        'codEmpleado' => '1003',
        'nombreEmpleado' => 'Carla',
        'apellidosEmpleado' => 'Jiménez Araya', 
    ]);

    $grupo->asignarDocente($docente1);
    $grupo->asignarDocente($docente2);
    $grupo->asignarDocente($docente3); 

    expect($grupo->docente1->codEmpleado)->toEqual($docente1->codEmpleado);
    expect($grupo->docente2->codEmpleado)->toEqual($docente2->codEmpleado);
    expect($grupo->docente3)->toBeNull();
    
});

test('Un grupo con 14 estudiantes no puede abrirse', function () {
    $curso = new Curso()::factory()->create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = new Grupo()::factory()->create([
        'numeroGrupo' => '01',
        'ano' => 2026,
        'numeroCiclo' => 2,
        'abierto' => false,
        'curso_id'=> $curso->id,
    ]);

    $estudiantes = Estudiante::factory()->count(14)->create();

    foreach ($estudiantes as $estudiante) {
        EstudianteMatriculado::factory()->create([
            'estudiante_id' => $estudiante->id,
            'grupo_id' => $grupo->id,
        ]);
    }

    $docente1 = new Docente()::factory()->create([
        'codEmpleado' => '1001',
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    $grupo->asignarDocente($docente1);

    expect($grupo->abrirGrupo())->toBeFalse();
});


test('Un grupo con 15 estudiantes y un docente se abre', function () {
    $curso = new Curso()::factory()->create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = new Grupo()::factory()->create([
        'numeroGrupo' => '01',
        'ano' => 2026,
        'numeroCiclo' => 2,
        'abierto' => false,
        'curso_id'=> $curso->id,
    ]);

    $docente1 = new Docente()::factory()->create([
        'codEmpleado' => '1001',
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

     $estudiantes = Estudiante::factory()->count(15)->create();

    foreach ($estudiantes as $estudiante) {
        EstudianteMatriculado::factory()->create([
            'estudiante_id' => $estudiante->id,
            'grupo_id' => $grupo->id,
        ]);
    }

    $grupo->asignarDocente($docente1);
    expect($grupo->abrirGrupo())->toBeTrue();
});



