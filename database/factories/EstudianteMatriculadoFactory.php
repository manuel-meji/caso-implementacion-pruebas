<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\EstudianteMatriculado;
use App\Models\Grupo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EstudianteMatriculado>
 */
class EstudianteMatriculadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'estudiante_id' => Estudiante::factory(),
            'grupo_id' => Grupo::factory(),
            'nota' => fake()->randomFloat(2, 0, 100),
        ];
    }
}
