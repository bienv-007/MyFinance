<?php

namespace Tests\Feature\Api;

use App\Models\Categorie;
use App\Models\Depense;
use App\Models\Revenu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevenuTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_index_returns_only_own_revenus_with_pagination(): void
    {
        $user = $this->actingAsUser();
        Revenu::factory()->count(11)->create(['id_utilisateur' => $user->id_utilisateur]);
        Revenu::factory()->create(['id_utilisateur' => User::factory()->create()->id_utilisateur]);

        $this->getJson('/api/revenus')
            ->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 11)
            ->assertJsonPath('meta.last_page', 2);
    }

    public function test_store_creates_revenu_for_current_user(): void
    {
        $user = $this->actingAsUser();

        $this->postJson('/api/revenus', [
            'montant' => 1500.50,
            'source' => 'Salaire',
            'date_revenu' => '2026-08-01',
            'description' => 'Salaire du mois',
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.id_utilisateur', $user->id_utilisateur)
            ->assertJsonPath('data.source', 'Salaire');
    }

    public function test_store_validates_fields(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/revenus', ['montant' => -5, 'source' => '', 'date_revenu' => 'nope'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['montant', 'source', 'date_revenu']);
    }

    public function test_show_own_revenu(): void
    {
        $user = $this->actingAsUser();
        $revenu = Revenu::factory()->create(['id_utilisateur' => $user->id_utilisateur]);

        $this->getJson("/api/revenus/{$revenu->id_revenu}")
            ->assertStatus(200)
            ->assertJsonPath('data.id_revenu', $revenu->id_revenu);
    }

    public function test_cannot_access_other_users_revenu(): void
    {
        $this->actingAsUser();
        $other = Revenu::factory()->create(['id_utilisateur' => User::factory()->create()->id_utilisateur]);

        $this->getJson("/api/revenus/{$other->id_revenu}")->assertStatus(403);
        $this->putJson("/api/revenus/{$other->id_revenu}", [
            'montant' => 1,
            'source' => 'x',
            'date_revenu' => '2026-08-01',
        ])->assertStatus(403);
        $this->deleteJson("/api/revenus/{$other->id_revenu}")->assertStatus(403);
    }

    public function test_update_modifies_own_revenu(): void
    {
        $user = $this->actingAsUser();
        $revenu = Revenu::factory()->create(['id_utilisateur' => $user->id_utilisateur]);

        $this->putJson("/api/revenus/{$revenu->id_revenu}", [
            'montant' => 999,
            'source' => 'Autre',
            'date_revenu' => '2026-08-05',
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.montant', '999.00')
            ->assertJsonPath('data.source', 'Autre');
    }

    public function test_destroy_removes_own_revenu(): void
    {
        $user = $this->actingAsUser();
        $revenu = Revenu::factory()->create(['id_utilisateur' => $user->id_utilisateur]);

        $this->deleteJson("/api/revenus/{$revenu->id_revenu}")->assertStatus(200);
        $this->assertDatabaseMissing('revenus', ['id_revenu' => $revenu->id_revenu]);
    }
}

class DepenseTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_index_returns_only_own_depenses_with_categorie(): void
    {
        $user = $this->actingAsUser();
        $category = Categorie::factory()->create();
        Depense::factory()->count(5)->create(['id_utilisateur' => $user->id_utilisateur, 'id_categorie' => $category->id_categorie]);
        Depense::factory()->create(['id_utilisateur' => User::factory()->create()->id_utilisateur, 'id_categorie' => $category->id_categorie]);

        $this->getJson('/api/depenses')
            ->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.total', 5);
    }

    public function test_store_creates_depense_for_current_user(): void
    {
        $user = $this->actingAsUser();
        $category = Categorie::factory()->create();

        $this->postJson('/api/depenses', [
            'id_categorie' => $category->id_categorie,
            'montant' => 45.99,
            'date_depense' => '2026-08-02',
            'description' => 'Courses',
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.id_utilisateur', $user->id_utilisateur)
            ->assertJsonPath('data.categorie.id_categorie', $category->id_categorie);
    }

    public function test_store_rejects_invalid_categorie(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/depenses', [
            'id_categorie' => 99999,
            'montant' => 10,
            'date_depense' => '2026-08-02',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('id_categorie');
    }

    public function test_cannot_access_other_users_depense(): void
    {
        $this->actingAsUser();
        $category = Categorie::factory()->create();
        $other = Depense::factory()->create([
            'id_utilisateur' => User::factory()->create()->id_utilisateur,
            'id_categorie' => $category->id_categorie,
        ]);

        $this->getJson("/api/depenses/{$other->id_depense}")->assertStatus(403);
        $this->putJson("/api/depenses/{$other->id_depense}", [
            'id_categorie' => $category->id_categorie,
            'montant' => 1,
            'date_depense' => '2026-08-02',
        ])->assertStatus(403);
        $this->deleteJson("/api/depenses/{$other->id_depense}")->assertStatus(403);
    }

    public function test_update_modifies_own_depense(): void
    {
        $user = $this->actingAsUser();
        $category = Categorie::factory()->create();
        $depense = Depense::factory()->create(['id_utilisateur' => $user->id_utilisateur, 'id_categorie' => $category->id_categorie]);

        $this->putJson("/api/depenses/{$depense->id_depense}", [
            'id_categorie' => $category->id_categorie,
            'montant' => 25,
            'date_depense' => '2026-08-10',
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.montant', '25.00');
    }

    public function test_destroy_removes_own_depense(): void
    {
        $user = $this->actingAsUser();
        $category = Categorie::factory()->create();
        $depense = Depense::factory()->create(['id_utilisateur' => $user->id_utilisateur, 'id_categorie' => $category->id_categorie]);

        $this->deleteJson("/api/depenses/{$depense->id_depense}")->assertStatus(200);
        $this->assertDatabaseMissing('depenses', ['id_depense' => $depense->id_depense]);
    }
}
