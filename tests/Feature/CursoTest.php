<?php

use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('se puede guardar un curso enviando datos mediante una peticion post a la url', function () {
    $data = [
        'sigla' => 'IF3000',
        'nombreCurso' => 'Programacion I',
        'creditos' => 4,
    ];

    $response = $this->post('/cursos', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('cursos', [
        'sigla' => 'IF3000',
        'nombreCurso' => 'Programacion I',
        'creditos' => 4,
    ]);

    $curso = Curso::where('sigla', 'IF3000')->first();
    expect($curso)->not->toBeNull();
    expect($curso->nombreCurso)->toBe('Programacion I');
    expect($curso->creditos)->toBe(4);
});

test('se puede guardar un curso enviando datos mediante parametros en la url', function () {
    $data = [
        'sigla' => 'IF4000',
        'nombreCurso' => 'Bases de Datos',
        'creditos' => 3,
    ];

    $response = $this->post('/cursos?'.http_build_query($data));

    $response->assertStatus(201);

    $this->assertDatabaseHas('cursos', [
        'sigla' => 'IF4000',
        'nombreCurso' => 'Bases de Datos',
        'creditos' => 3,
    ]);
});
