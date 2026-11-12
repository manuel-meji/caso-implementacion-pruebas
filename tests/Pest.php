<?php

use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Grupo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function crearCursoIF6100(): Curso
{
    return Curso::factory()->create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);
}

function crearGrupo01(Curso $curso): Grupo
{
    return Grupo::factory()->create([
        'curso_id' => $curso->id,
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);
}

function crearDocente1001(): Docente
{
    return Docente::factory()->create([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);
}

function crearDocente1002(): Docente
{
    return Docente::factory()->create([
        'codEmpleado' => 1002,
        'nombreEmpleado' => 'Luis',
        'apellidosEmpleado' => 'Rojas Solano',
    ]);
}

function crearDocente1003(): Docente
{
    return Docente::factory()->create([
        'codEmpleado' => 1003,
        'nombreEmpleado' => 'Carla',
        'apellidosEmpleado' => 'Jiménez Araya',
    ]);
}

function crearEstudianteC12345(Docente $consejero): Estudiante
{
    return Estudiante::factory()->create([
        'carne' => 'C12345',
        'nombre' => 'María',
        'apellidos' => 'Solís Quesada',
        'consejero_id' => $consejero->id,
    ]);
}
