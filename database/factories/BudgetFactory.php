<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
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
            'periode' => fake()->randomElement(['2026-01', '2026-02', '2026-03', '2026-Q1', '2026-Annuel']),
            'montant_total' => fake()->randomFloat(2, 100, 100000),
            'date_debut' => fake()->date(),
            'date_fin' => fake()->date('Y-m-d', '+1 year'),
        ];
    }
}
