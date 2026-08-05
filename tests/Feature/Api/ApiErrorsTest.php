<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiErrorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_api_route_returns_json_404(): void
    {
        $this->actingAs(User::factory()->create());

        $this->getJson('/api/does-not-exist')
            ->assertStatus(404)
            ->assertHeader('content-type', 'application/json');
    }

    public function test_unknown_api_post_route_returns_json_404(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson('/api/does-not-exist', [])
            ->assertStatus(404)
            ->assertHeader('content-type', 'application/json');
    }

    public function test_show_on_missing_model_returns_404(): void
    {
        $this->actingAs(User::factory()->create());

        $this->getJson('/api/categories/99999')->assertStatus(404);
        $this->getJson('/api/revenus/99999')->assertStatus(404);
        $this->getJson('/api/depenses/99999')->assertStatus(404);
        $this->getJson('/api/budgets/99999')->assertStatus(404);
        $this->getJson('/api/revenu-previsions/99999')->assertStatus(404);
        $this->getJson('/api/depense-previsions/99999')->assertStatus(404);
    }
}
