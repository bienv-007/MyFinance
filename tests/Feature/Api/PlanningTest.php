<?php

namespace Tests\Feature\Api;

use App\Models\Budget;
use App\Models\Categorie;
use App\Models\DepensePrevision;
use App\Models\RevenuPrevision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    private function budgetPayload(): array
    {
        return [
            'periode' => '2026-08',
            'montant_total' => 5000,
            'date_debut' => '2026-08-01',
            'date_fin' => '2026-08-31',
        ];
    }

    public function test_index_returns_only_the_users_single_budget(): void
    {
        $user = $this->actingAsUser();
        Budget::factory()->create(['id_utilisateur' => $user->id_utilisateur]);
        Budget::factory()->create(['id_utilisateur' => User::factory()->create()->id_utilisateur]);

        $this->getJson('/api/budgets')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonStructure(['meta' => ['current_page', 'last_page', 'total']]);
    }

    public function test_store_creates_budget(): void
    {
        $user = $this->actingAsUser();

        $this->postJson('/api/budgets', $this->budgetPayload())
            ->assertStatus(201)
            ->assertJsonPath('data.id_utilisateur', $user->id_utilisateur)
            ->assertJsonPath('data.periode', '2026-08');
    }

    public function test_store_validates_dates_order(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/budgets', [
            ...$this->budgetPayload(),
            'date_fin' => '2026-07-31',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('date_fin');
    }

    public function test_store_rejects_a_second_budget_for_the_same_user(): void
    {
        $user = $this->actingAsUser();
        Budget::factory()->create(['id_utilisateur' => $user->id_utilisateur]);

        $this->postJson('/api/budgets', $this->budgetPayload())
            ->assertStatus(422)
            ->assertJsonValidationErrors('budget');
    }

    public function test_show_own_budget(): void
    {
        $user = $this->actingAsUser();
        $budget = Budget::factory()->create(['id_utilisateur' => $user->id_utilisateur]);

        $this->getJson("/api/budgets/{$budget->id_budget}")
            ->assertStatus(200)
            ->assertJsonPath('data.id_budget', $budget->id_budget);
    }

    public function test_update_is_forbidden_for_other_users_budget(): void
    {
        $this->actingAsUser();
        $other = Budget::factory()->create(['id_utilisateur' => User::factory()->create()->id_utilisateur]);

        $this->putJson("/api/budgets/{$other->id_budget}", $this->budgetPayload())
            ->assertStatus(403);
    }

    public function test_update_modifies_own_budget(): void
    {
        $user = $this->actingAsUser();
        $budget = Budget::factory()->create(['id_utilisateur' => $user->id_utilisateur]);

        $this->putJson("/api/budgets/{$budget->id_budget}", $this->budgetPayload())
            ->assertStatus(200)
            ->assertJsonPath('data.periode', '2026-08');
    }

    public function test_cannot_show_or_delete_other_users_budget(): void
    {
        $this->actingAsUser();
        $other = Budget::factory()->create(['id_utilisateur' => User::factory()->create()->id_utilisateur]);

        $this->getJson("/api/budgets/{$other->id_budget}")->assertStatus(403);
        $this->deleteJson("/api/budgets/{$other->id_budget}")->assertStatus(403);
    }

    public function test_destroy_removes_own_budget(): void
    {
        $user = $this->actingAsUser();
        $budget = Budget::factory()->create(['id_utilisateur' => $user->id_utilisateur]);

        $this->deleteJson("/api/budgets/{$budget->id_budget}")->assertStatus(200);
        $this->assertDatabaseMissing('budgets', ['id_budget' => $budget->id_budget]);
    }
}

class RevenuPrevisionTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_index_returns_only_own_previsions(): void
    {
        $user = $this->actingAsUser();
        RevenuPrevision::factory()->count(2)->create(['id_utilisateur' => $user->id_utilisateur]);
        RevenuPrevision::factory()->create(['id_utilisateur' => User::factory()->create()->id_utilisateur]);

        $this->getJson('/api/revenu-previsions')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2);
    }

    public function test_store_creates_revenu_prevision(): void
    {
        $user = $this->actingAsUser();

        $this->postJson('/api/revenu-previsions', [
            'montant_previsionnel' => 1200,
            'source_previsionnelle' => 'Bonus',
            'date_previsionnelle' => '2026-09-15',
            'description' => 'Bonus annuel',
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.id_utilisateur', $user->id_utilisateur)
            ->assertJsonPath('data.source_previsionnelle', 'Bonus');
    }

    public function test_show_own_revenu_prevision(): void
    {
        $user = $this->actingAsUser();
        $prevision = RevenuPrevision::factory()->create(['id_utilisateur' => $user->id_utilisateur]);

        $this->getJson("/api/revenu-previsions/{$prevision->id_revenu_prevision}")
            ->assertStatus(200)
            ->assertJsonPath('data.id_revenu_prevision', $prevision->id_revenu_prevision);
    }

    public function test_cannot_access_other_users_revenu_prevision(): void
    {
        $this->actingAsUser();
        $other = RevenuPrevision::factory()->create(['id_utilisateur' => User::factory()->create()->id_utilisateur]);

        $this->getJson("/api/revenu-previsions/{$other->id_revenu_prevision}")->assertStatus(403);
        $this->putJson("/api/revenu-previsions/{$other->id_revenu_prevision}", [
            'montant_previsionnel' => 1,
            'source_previsionnelle' => 'x',
            'date_previsionnelle' => '2026-09-01',
            'description' => 'Tentative interdite',
        ])->assertStatus(403);
        $this->deleteJson("/api/revenu-previsions/{$other->id_revenu_prevision}")->assertStatus(403);
    }

    public function test_update_and_destroy_own_revenu_prevision(): void
    {
        $user = $this->actingAsUser();
        $prevision = RevenuPrevision::factory()->create(['id_utilisateur' => $user->id_utilisateur]);

        $this->putJson("/api/revenu-previsions/{$prevision->id_revenu_prevision}", [
            'montant_previsionnel' => 500,
            'source_previsionnelle' => 'Prime',
            'date_previsionnelle' => '2026-10-01',
            'description' => 'Prime attendue',
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.montant_previsionnel', '500.00');

        $this->deleteJson("/api/revenu-previsions/{$prevision->id_revenu_prevision}")->assertStatus(200);
        $this->assertDatabaseMissing('revenu_previsions', ['id_revenu_prevision' => $prevision->id_revenu_prevision]);
    }
}

class DepensePrevisionTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_store_creates_depense_prevision_with_categorie(): void
    {
        $user = $this->actingAsUser();
        $category = Categorie::factory()->create();

        $this->postJson('/api/depense-previsions', [
            'id_categorie' => $category->id_categorie,
            'montant_previsionnel' => 300,
            'date_previsionnelle' => '2026-09-20',
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.id_utilisateur', $user->id_utilisateur)
            ->assertJsonPath('data.categorie.id_categorie', $category->id_categorie);
    }

    public function test_show_own_depense_prevision(): void
    {
        $user = $this->actingAsUser();
        $prevision = DepensePrevision::factory()->create(['id_utilisateur' => $user->id_utilisateur]);

        $this->getJson("/api/depense-previsions/{$prevision->id_depense_prevision}")
            ->assertStatus(200)
            ->assertJsonPath('data.id_depense_prevision', $prevision->id_depense_prevision);
    }

    public function test_cannot_access_other_users_depense_prevision(): void
    {
        $this->actingAsUser();
        $other = DepensePrevision::factory()->create(['id_utilisateur' => User::factory()->create()->id_utilisateur]);

        $this->getJson("/api/depense-previsions/{$other->id_depense_prevision}")->assertStatus(403);
        $this->deleteJson("/api/depense-previsions/{$other->id_depense_prevision}")->assertStatus(403);
    }

    public function test_index_filters_by_search_on_categorie_name(): void
    {
        $user = $this->actingAsUser();
        $category = Categorie::factory()->create(['nom_categorie' => 'Loyer']);
        DepensePrevision::factory()->create(['id_utilisateur' => $user->id_utilisateur, 'id_categorie' => $category->id_categorie]);

        $this->getJson('/api/depense-previsions?search=Loyer')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
