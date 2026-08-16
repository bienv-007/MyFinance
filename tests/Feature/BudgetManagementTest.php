<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BudgetManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_budget_dashboard_and_statistics(): void
    {
        $user = $this->actingAsBudgetUser();
        $user->budgets()->create([
            'periode' => 'Août 2026',
            'montant_total' => 5000,
            'date_debut' => today()->subDay(),
            'date_fin' => today()->addDays(10),
        ]);

        $this->get(route('budgets.index'))
            ->assertOk()
            ->assertSee('Budgets créés')
            ->assertSee('Budget actif')
            ->assertSee('Août 2026')
            ->assertSee('5 000,00');
    }

    public function test_budget_creation_rejects_invalid_amount_and_date_range(): void
    {
        $this->actingAsBudgetUser();

        $this->from(route('budgets.create'))
            ->post(route('budgets.store'), [
                'periode' => 'Août 2026',
                'montant_total' => 0,
                'date_debut' => '2026-08-31',
                'date_fin' => '2026-08-01',
            ])
            ->assertRedirect(route('budgets.create'))
            ->assertSessionHasErrors(['montant_total', 'date_fin']);
    }

    public function test_budget_creation_rejects_a_second_budget_for_the_same_user(): void
    {
        $user = $this->actingAsBudgetUser();
        $user->budgets()->create([
            'periode' => 'Août 2026',
            'montant_total' => 5000,
            'date_debut' => '2026-08-01',
            'date_fin' => '2026-08-31',
        ]);

        $this->from(route('budgets.create'))
            ->post(route('budgets.store'), [
                'periode' => 'Septembre 2026',
                'montant_total' => 3200,
                'date_debut' => '2026-09-01',
                'date_fin' => '2026-09-30',
            ])
            ->assertRedirect(route('budgets.create'))
            ->assertSessionHasErrors('budget');

        $this->assertSame(1, $user->budgets()->count());
    }

    public function test_authenticated_user_can_create_update_and_delete_a_budget(): void
    {
        $user = $this->actingAsBudgetUser();
        $payload = [
            'periode' => 'Septembre 2026',
            'montant_total' => 3200,
            'date_debut' => '2026-09-01',
            'date_fin' => '2026-09-30',
        ];

        $this->post(route('budgets.store'), $payload)
            ->assertRedirect(route('budgets.index'))
            ->assertSessionHas('success');

        $budget = $user->budgets()->firstOrFail();
        $this->assertDatabaseHas('budgets', [
            'id_budget' => $budget->id_budget,
            'periode' => 'Septembre 2026',
        ]);

        $this->put(route('budgets.update', $budget), [
            ...$payload,
            'periode' => 'Septembre révisé',
        ])->assertRedirect(route('budgets.index'));

        $this->assertDatabaseHas('budgets', [
            'id_budget' => $budget->id_budget,
            'periode' => 'Septembre révisé',
        ]);

        $this->delete(route('budgets.destroy', $budget))
            ->assertRedirect(route('budgets.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('budgets', ['id_budget' => $budget->id_budget]);
    }

    public function test_user_cannot_view_another_users_budget(): void
    {
        $this->actingAsBudgetUser();
        $otherUser = $this->createBudgetUser('other@example.com');
        $budget = $otherUser->budgets()->create([
            'periode' => 'Budget privé',
            'montant_total' => 100,
            'date_debut' => '2026-08-01',
            'date_fin' => '2026-08-31',
        ]);

        $this->get(route('budgets.show', $budget))->assertNotFound();
        $this->get(route('budgets.edit', $budget))->assertNotFound();
    }

    public function test_budget_api_resource_is_available_for_the_authenticated_user(): void
    {
        $user = $this->actingAsBudgetUser();

        $this->postJson('/api/budgets', [
            'periode' => 'Août 2026',
            'montant_total' => 1800,
            'date_debut' => today()->toDateString(),
            'date_fin' => today()->addDays(30)->toDateString(),
        ])
            ->assertCreated()
            ->assertJsonPath('data.id_utilisateur', $user->id_utilisateur)
            ->assertJsonPath('data.date_debut', today()->toDateString())
            ->assertJsonPath('data.solde', '1800.00')
            ->assertJsonPath('data.statut', 'En cours');
    }

    public function test_budget_update_can_reset_the_balance_to_the_new_amount(): void
    {
        $user = $this->actingAsBudgetUser();
        $budget = $user->budgets()->create([
            'periode' => 'Août 2026',
            'montant_total' => 1000,
            'solde' => 250,
            'date_debut' => '2026-08-01',
            'date_fin' => '2026-08-31',
        ]);

        $this->put(route('budgets.update', $budget), [
            'periode' => 'Août 2026',
            'montant_total' => 1800,
            'date_debut' => '2026-08-01',
            'date_fin' => '2026-08-31',
            'reinitialiser_solde' => true,
        ])->assertRedirect(route('budgets.index'));

        $this->assertDatabaseHas('budgets', [
            'id_budget' => $budget->id_budget,
            'montant_total' => 1800,
            'solde' => 1800,
        ]);
    }

    private function actingAsBudgetUser(): User
    {
        $user = $this->createBudgetUser();
        $this->actingAs($user);

        return $user;
    }

    private function createBudgetUser(string $email = 'budget@example.com'): User
    {
        return User::create([
            'nom' => 'Dupont',
            'prenom' => 'Alex',
            'email' => $email,
            'mot_de_passe' => Hash::make('password'),
            'date_creation' => now(),
        ]);
    }
}
