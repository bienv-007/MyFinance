<?php

namespace Database\Factories;

use App\Models\RevenuPrevision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RevenuPrevision>
 */
class RevenuPrevisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_utilisateur' => User::factory(),
            'montant_previsionnel' => fake()->randomFloat(2, 1, 5000),
            'source_previsionnelle' => fake()->words(2, true),
            'date_previsionnelle' => fake()->date(),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
