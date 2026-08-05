<?php

namespace Tests\Feature\Api;

use App\Models\Categorie;
use App\Models\Depense;
use App\Models\DepensePrevision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_index_returns_paginated_collection_with_meta(): void
    {
        $user = $this->actingAsUser();
        Categorie::factory()->count(12)->create();

        $this->getJson('/api/categories')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id_categorie', 'nom_categorie']],
                'meta' => ['current_page', 'last_page', 'total'],
            ])
            ->assertJsonPath('meta.total', 12)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonCount(10, 'data');
    }

    public function test_index_supports_search(): void
    {
        $this->actingAsUser();
        Categorie::factory()->create(['nom_categorie' => 'Transport']);
        Categorie::factory()->create(['nom_categorie' => 'Nourriture']);

        $this->getJson('/api/categories?search=Transport')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nom_categorie', 'Transport');
    }

    public function test_store_creates_category(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/categories', ['nom_categorie' => 'Loisirs'])
            ->assertStatus(201)
            ->assertJsonPath('data.nom_categorie', 'Loisirs');

        $this->assertDatabaseHas('categories', ['nom_categorie' => 'Loisirs']);
    }

    public function test_store_validates_name(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/categories', ['nom_categorie' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors('nom_categorie');
    }

    public function test_show_returns_category(): void
    {
        $this->actingAsUser();
        $category = Categorie::factory()->create();

        $this->getJson("/api/categories/{$category->id_categorie}")
            ->assertStatus(200)
            ->assertJsonPath('data.id_categorie', $category->id_categorie);
    }

    public function test_update_modifies_category(): void
    {
        $this->actingAsUser();
        $category = Categorie::factory()->create(['nom_categorie' => 'Ancien']);

        $this->putJson("/api/categories/{$category->id_categorie}", ['nom_categorie' => 'Nouveau'])
            ->assertStatus(200)
            ->assertJsonPath('data.nom_categorie', 'Nouveau');
    }

    public function test_destroy_unused_category(): void
    {
        $this->actingAsUser();
        $category = Categorie::factory()->create();

        $this->deleteJson("/api/categories/{$category->id_categorie}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('categories', ['id_categorie' => $category->id_categorie]);
    }

    public function test_destroy_category_used_by_depense_is_rejected(): void
    {
        $this->actingAsUser();
        $category = Categorie::factory()->create();
        Depense::factory()->create(['id_categorie' => $category->id_categorie]);

        $this->deleteJson("/api/categories/{$category->id_categorie}")
            ->assertStatus(422)
            ->assertJsonPath('message', 'Catégorie utilisée par des dépenses ou des prévisions.');

        $this->assertDatabaseHas('categories', ['id_categorie' => $category->id_categorie]);
    }

    public function test_destroy_category_used_by_depense_prevision_is_rejected(): void
    {
        $this->actingAsUser();
        $category = Categorie::factory()->create();
        DepensePrevision::factory()->create(['id_categorie' => $category->id_categorie]);

        $this->deleteJson("/api/categories/{$category->id_categorie}")
            ->assertStatus(422);

        $this->assertDatabaseHas('categories', ['id_categorie' => $category->id_categorie]);
    }
}
