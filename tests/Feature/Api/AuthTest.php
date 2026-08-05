<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_a_user_and_returns_201(): void
    {
        $payload = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john@example.com',
            'mot_de_passe' => 'secret123',
            'mot_de_passe_confirmation' => 'secret123',
        ];

        $this->postJson('/api/auth/register', $payload)
            ->assertStatus(201)
            ->assertJsonPath('message', 'Utilisateur créé avec succès.')
            ->assertJsonStructure(['data' => ['id_utilisateur', 'nom', 'prenom', 'email']])
            ->assertJsonPath('data.email', 'john@example.com');

        $this->assertDatabaseHas('utilisateurs', ['email' => 'john@example.com']);
    }

    public function test_register_validates_fields(): void
    {
        $this->postJson('/api/auth/register', [
            'nom' => '',
            'email' => 'not-an-email',
            'mot_de_passe' => 'short',
            'mot_de_passe_confirmation' => 'different',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['nom', 'email', 'mot_de_passe']);
    }

    public function test_login_succeeds_with_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'login@example.com', 'mot_de_passe' => 'secret123']);

        $this->postJson('/api/auth/login', [
            'email' => 'login@example.com',
            'mot_de_passe' => 'secret123',
        ])
            ->assertStatus(200)
            ->assertJsonPath('message', 'Connexion réussie.')
            ->assertJsonPath('data.email', 'login@example.com');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'login@example.com', 'mot_de_passe' => 'secret123']);

        $this->postJson('/api/auth/login', [
            'email' => 'login@example.com',
            'mot_de_passe' => 'wrong-password',
        ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Identifiants invalides.');
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/auth/me')
            ->assertStatus(200)
            ->assertJsonPath('data.id_utilisateur', $user->id_utilisateur);
    }

    public function test_me_returns_401_when_unauthenticated(): void
    {
        $this->getJson('/api/auth/me')->assertStatus(401);
    }

    public function test_logout_ends_the_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/auth/logout')
            ->assertStatus(200)
            ->assertJsonPath('message', 'Déconnexion réussie.');
    }

    public function test_protected_endpoints_require_authentication(): void
    {
        $this->getJson('/api/categories')->assertStatus(401);
        $this->postJson('/api/categories', ['nom_categorie' => 'x'])->assertStatus(401);
        $this->getJson('/api/budgets')->assertStatus(401);
        $this->getJson('/api/revenu-previsions')->assertStatus(401);
        $this->getJson('/api/depense-previsions')->assertStatus(401);
    }
}
