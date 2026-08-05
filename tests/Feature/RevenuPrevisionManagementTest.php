<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RevenuPrevisionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard_and_statistics(): void
    {
        $user = $this->actingAsPrevisionUser();
        $user->revenuPrevisions()->create([
            'montant_previsionnel' => 2500,
            'source_previsionnelle' => 'Salaire',
            'date_previsionnelle' => today()->addDays(5),
            'description' => 'Salaire du mois',
        ]);

        $this->get(route('revenu-previsions.index'))
            ->assertOk()
            ->assertSee('Prévisions de revenus')
            ->assertSee('Salaire du mois')
            ->assertSee('2 500,00');
    }

    public function test_creation_requires_positive_amount_and_description(): void
    {
        $this->actingAsPrevisionUser();

        $this->from(route('revenu-previsions.create'))
            ->post(route('revenu-previsions.store'), [
                'montant_previsionnel' => 0,
                'source_previsionnelle' => 'Prime',
                'date_previsionnelle' => today()->toDateString(),
                'description' => '',
            ])
            ->assertRedirect(route('revenu-previsions.create'))
            ->assertSessionHasErrors(['montant_previsionnel', 'description']);
    }

    public function test_user_can_create_update_and_delete_a_revenue_prevision(): void
    {
        $user = $this->actingAsPrevisionUser();
        $payload = [
            'montant_previsionnel' => 1500,
            'source_previsionnelle' => 'Activité',
            'date_previsionnelle' => today()->addDays(10)->toDateString(),
            'description' => 'Mission freelance',
        ];

        $this->post(route('revenu-previsions.store'), $payload)
            ->assertRedirect(route('revenu-previsions.index'))
            ->assertSessionHas('success');

        $prevision = $user->revenuPrevisions()->firstOrFail();
        $this->put(route('revenu-previsions.update', $prevision), [
            ...$payload,
            'description' => 'Mission freelance révisée',
        ])->assertRedirect(route('revenu-previsions.index'));

        $this->assertDatabaseHas('revenu_previsions', [
            'id_revenu_prevision' => $prevision->id_revenu_prevision,
            'description' => 'Mission freelance révisée',
        ]);

        $this->delete(route('revenu-previsions.destroy', $prevision))
            ->assertRedirect(route('revenu-previsions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('revenu_previsions', ['id_revenu_prevision' => $prevision->id_revenu_prevision]);
    }

    public function test_api_exposes_aliases_filters_and_status(): void
    {
        $user = $this->actingAsPrevisionUser();

        $this->postJson('/api/revenu-previsions', [
            'montant_prevision' => 75,
            'source_prevision' => 'Bonus',
            'date_prevision' => today()->toDateString(),
            'description' => 'Bonus du jour',
        ])
            ->assertCreated()
            ->assertJsonPath('data.id_utilisateur', $user->id_utilisateur)
            ->assertJsonPath('data.id_prevision_revenu', 1)
            ->assertJsonPath('data.montant_prevision', '75.00')
            ->assertJsonPath('data.date_prevision', today()->toDateString())
            ->assertJsonPath('data.statut', "Aujourd'hui");

        $this->getJson('/api/revenu-previsions?source=Bonus')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('stats.total', 1);
    }

    public function test_user_can_mark_a_prevision_as_received_without_deleting_it(): void
    {
        $user = $this->actingAsPrevisionUser();
        $prevision = $user->revenuPrevisions()->create([
            'montant_previsionnel' => 900,
            'source_previsionnelle' => 'Commission',
            'date_previsionnelle' => today()->toDateString(),
            'description' => 'Commission reçue',
        ]);

        $this->post(route('revenu-previsions.receive', $prevision))
            ->assertRedirect(route('revenu-previsions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('revenus', [
            'id_utilisateur' => $user->id_utilisateur,
            'montant' => 900,
            'source' => 'Commission',
            'description' => 'Commission reçue',
        ]);
        $this->assertDatabaseHas('revenu_previsions', [
            'id_revenu_prevision' => $prevision->id_revenu_prevision,
        ]);
        $this->assertSame('Réalisée', $prevision->fresh()->statut);
    }

    private function actingAsPrevisionUser(): User
    {
        $user = User::create([
            'nom' => 'Martin',
            'prenom' => 'Camille',
            'email' => 'revenu-prevision@example.com',
            'mot_de_passe' => Hash::make('password'),
            'date_creation' => now(),
        ]);
        $this->actingAs($user);

        return $user;
    }
}
