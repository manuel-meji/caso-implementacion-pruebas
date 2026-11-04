<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    /**
     * Almacena un nuevo curso en la base de datos.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sigla' => ['required', 'string', 'max:255', 'unique:cursos,sigla'],
            'nombreCurso' => ['required', 'string', 'max:255'],
            'creditos' => ['required', 'integer', 'min:1'],
        ]);

        $curso = Curso::create($validated);

        return response()->json([
            'message' => 'Curso creado exitosamente',
            'data' => $curso,
        ], 201);
    }
}
