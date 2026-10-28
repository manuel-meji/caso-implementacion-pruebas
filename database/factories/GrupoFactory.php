<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Docente;
use App\Models\Grupo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grupo>
 */
class GrupoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numeroCiclo' => fake()->numberBetween(1, 2),
            'ano' => 2026,
            'numeroGrupo' => fake()->numberBetween(1, 10),
            'abierto' => fake()->boolean(),
            'curso_id' => Curso::factory(),
            'docente1_id' => Docente::factory(),
            'docente2_id' => null,
        ];
    }
}
