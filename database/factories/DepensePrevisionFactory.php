<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\DepensePrevision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DepensePrevision>
 */
class DepensePrevisionFactory extends Factory
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
            'id_categorie' => Categorie::factory(),
            'montant_previsionnel' => fake()->randomFloat(2, 1, 5000),
            'date_previsionnelle' => fake()->date(),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
