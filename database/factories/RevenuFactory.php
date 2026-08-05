<?php

namespace Database\Factories;

use App\Models\Revenu;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Revenu>
 */
class RevenuFactory extends Factory
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
            'montant' => fake()->randomFloat(2, 1, 5000),
            'source' => fake()->words(2, true),
            'date_revenu' => fake()->date(),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
