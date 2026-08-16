<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Budget;
use App\Models\Depense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DepensePrevisionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_prevision_dashboard_and_statistics(): void
    {
        $user = $this->actingAsPrevisionUser();
        $category = Categorie::create(['nom_categorie' => 'Maison']);
        $user->depensePrevisions()->create([
            'id_categorie' => $category->id_categorie,
            'montant_previsionnel' => 250,
            'date_previsionnelle' => today()->addDay(),
            'description' => 'Achat maison',
        ]);

        $this->get(route('depense-previsions.index'))
            ->assertOk()
            ->assertSee('Prévisions de dépenses')
            ->assertSee('Achat maison')
            ->assertSee('250,00');
    }

    public function test_creation_requires_positive_amount_and_description(): void
    {
        $this->actingAsPrevisionUser();
        $category = Categorie::create(['nom_categorie' => 'Transport']);

        $this->from(route('depense-previsions.create'))
            ->post(route('depense-previsions.store'), [
                'id_categorie' => $category->id_categorie,
                'montant_previsionnel' => 0,
                'date_previsionnelle' => today()->toDateString(),
                'description' => '',
            ])
            ->assertRedirect(route('depense-previsions.create'))
            ->assertSessionHasErrors(['montant_previsionnel', 'description']);
    }

    public function test_authenticated_user_can_create_update_and_delete_a_prevision(): void
    {
        $user = $this->actingAsPrevisionUser();
        $category = Categorie::create(['nom_categorie' => 'Loisirs']);
        $payload = [
            'id_categorie' => $category->id_categorie,
            'montant_previsionnel' => 1500,
            'date_previsionnelle' => today()->addDays(15)->toDateString(),
            'description' => 'Voyage',
        ];

        $this->post(route('depense-previsions.store'), $payload)
            ->assertRedirect(route('depense-previsions.index'))
            ->assertSessionHas('success');

        $prevision = $user->depensePrevisions()->firstOrFail();
        $this->assertDatabaseHas('depense_previsions', [
            'id_depense_prevision' => $prevision->id_depense_prevision,
            'description' => 'Voyage',
        ]);

        $this->put(route('depense-previsions.update', $prevision), [
            ...$payload,
            'description' => 'Voyage révisé',
        ])->assertRedirect(route('depense-previsions.index'));

        $this->assertDatabaseHas('depense_previsions', [
            'id_depense_prevision' => $prevision->id_depense_prevision,
            'description' => 'Voyage révisé',
        ]);

        $this->delete(route('depense-previsions.destroy', $prevision))
            ->assertRedirect(route('depense-previsions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('depense_previsions', ['id_depense_prevision' => $prevision->id_depense_prevision]);
    }

    public function test_api_exposes_canonical_prevision_aliases_and_status(): void
    {
        $user = $this->actingAsPrevisionUser();
        $category = Categorie::create(['nom_categorie' => 'Santé']);

        $this->postJson('/api/depense-previsions', [
            'id_categorie' => $category->id_categorie,
            'montant_prevision' => 75,
            'date_prevision' => today()->toDateString(),
            'description' => 'Pharmacie',
        ])
            ->assertCreated()
            ->assertJsonPath('data.id_utilisateur', $user->id_utilisateur)
            ->assertJsonPath('data.id_prevision', 1)
            ->assertJsonPath('data.montant_prevision', '75.00')
            ->assertJsonPath('data.date_prevision', today()->toDateString())
            ->assertJsonPath('data.statut', "Aujourd'hui");
    }

    public function test_user_cannot_access_another_users_prevision(): void
    {
        $this->actingAsPrevisionUser();
        $other = $this->createPrevisionUser('other-prevision@example.com');
        $category = Categorie::create(['nom_categorie' => 'Autre']);
        $prevision = $other->depensePrevisions()->create([
            'id_categorie' => $category->id_categorie,
            'montant_previsionnel' => 100,
            'date_previsionnelle' => today()->addDay(),
            'description' => 'Privée',
        ]);

        $this->get(route('depense-previsions.show', $prevision))->assertNotFound();
        $this->getJson("/api/depense-previsions/{$prevision->id_depense_prevision}")->assertForbidden();
    }

    public function test_user_can_validate_a_prevision_and_register_a_depense(): void
    {
        $user = $this->actingAsPrevisionUser();
        $category = Categorie::create(['nom_categorie' => 'Alimentation']);
        $prevision = $user->depensePrevisions()->create([
            'id_categorie' => $category->id_categorie,
            'montant_previsionnel' => 325.50,
            'date_previsionnelle' => today()->toDateString(),
            'description' => 'Courses du mois',
        ]);
        $this->createBudget($user, 500, today()->subDay(), today()->addDay());

        $this->post(route('depense-previsions.validate', $prevision))
            ->assertRedirect(route('depense-previsions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('depenses', [
            'id_utilisateur' => $user->id_utilisateur,
            'id_categorie' => $category->id_categorie,
            'montant' => 325.50,
            'description' => 'Courses du mois',
        ]);
        $depense = Depense::query()->where('id_utilisateur', $user->id_utilisateur)->firstOrFail();
        $this->assertSame(today()->toDateString(), $depense->date_depense->toDateString());
        $this->assertDatabaseMissing('depense_previsions', [
            'id_depense_prevision' => $prevision->id_depense_prevision,
        ]);
        $this->assertSame(1, Depense::query()->where('id_utilisateur', $user->id_utilisateur)->count());
        $this->assertDatabaseHas('budgets', ['id_budget' => $user->budgets()->firstOrFail()->id_budget, 'solde' => 174.50]);
    }

    public function test_api_can_validate_a_prevision_and_return_the_created_depense(): void
    {
        $user = $this->actingAsPrevisionUser();
        $category = Categorie::create(['nom_categorie' => 'Transport']);
        $prevision = $user->depensePrevisions()->create([
            'id_categorie' => $category->id_categorie,
            'montant_previsionnel' => 80,
            'date_previsionnelle' => today()->addDay()->toDateString(),
            'description' => 'Taxi',
        ]);
        $this->createBudget($user, 100, today(), today()->addDays(2));

        $this->postJson("/api/depense-previsions/{$prevision->id_depense_prevision}/validate")
            ->assertCreated()
            ->assertJsonPath('data.id_utilisateur', $user->id_utilisateur)
            ->assertJsonPath('data.id_categorie', $category->id_categorie)
            ->assertJsonPath('data.montant', '80.00')
            ->assertJsonStructure(['data' => ['id_depense', 'date_depense']]);

        $this->assertDatabaseHas('depenses', [
            'id_utilisateur' => $user->id_utilisateur,
            'description' => 'Taxi',
        ]);
        $this->assertDatabaseMissing('depense_previsions', [
            'id_depense_prevision' => $prevision->id_depense_prevision,
        ]);
    }

    public function test_prevision_validation_requires_a_budget_for_its_date(): void
    {
        $user = $this->actingAsPrevisionUser();
        $category = Categorie::create(['nom_categorie' => 'Santé']);
        $prevision = $user->depensePrevisions()->create([
            'id_categorie' => $category->id_categorie,
            'montant_previsionnel' => 100,
            'date_previsionnelle' => today()->toDateString(),
            'description' => 'Consultation',
        ]);

        $this->from(route('depense-previsions.index'))
            ->post(route('depense-previsions.validate', $prevision))
            ->assertRedirect(route('depense-previsions.index'))
            ->assertSessionHasErrors('date_previsionnelle');

        $this->assertDatabaseHas('depense_previsions', ['id_depense_prevision' => $prevision->id_depense_prevision]);
        $this->assertDatabaseMissing('depenses', ['description' => 'Consultation']);
    }

    public function test_prevision_validation_requires_a_sufficient_budget_balance(): void
    {
        $user = $this->actingAsPrevisionUser();
        $category = Categorie::create(['nom_categorie' => 'Santé']);
        $prevision = $user->depensePrevisions()->create([
            'id_categorie' => $category->id_categorie,
            'montant_previsionnel' => 100,
            'date_previsionnelle' => today()->toDateString(),
            'description' => 'Consultation',
        ]);
        $this->createBudget($user, 99, today()->subDay(), today()->addDay());

        $this->post(route('depense-previsions.validate', $prevision))
            ->assertSessionHasErrors('montant_previsionnel');

        $this->assertDatabaseHas('depense_previsions', ['id_depense_prevision' => $prevision->id_depense_prevision]);
        $this->assertDatabaseMissing('depenses', ['description' => 'Consultation']);
    }

    public function test_validating_a_prevision_notifies_each_budget_usage_threshold_reached(): void
    {
        $user = $this->actingAsPrevisionUser();
        $category = Categorie::create(['nom_categorie' => 'Logement']);
        $prevision = $user->depensePrevisions()->create([
            'id_categorie' => $category->id_categorie,
            'montant_previsionnel' => 1000,
            'date_previsionnelle' => today()->toDateString(),
            'description' => 'Loyer',
        ]);
        $this->createBudget($user, 1000, today()->subDay(), today()->addDay());

        $this->post(route('depense-previsions.validate', $prevision))->assertRedirect();

        $this->assertDatabaseCount('notifications', 4);
        foreach ([80, 90, 100] as $threshold) {
            $this->assertDatabaseHas('notifications', [
                'id_utilisateur' => $user->id_utilisateur,
                'type' => "budget_utilise_{$threshold}",
            ]);
        }
    }

    private function createBudget(User $user, float $solde, $dateDebut, $dateFin): Budget
    {
        return $user->budgets()->create([
            'periode' => 'Budget de test',
            'montant_total' => $solde,
            'solde' => $solde,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ]);
    }

    private function actingAsPrevisionUser(): User
    {
        $user = $this->createPrevisionUser();
        $this->actingAs($user);

        return $user;
    }

    private function createPrevisionUser(string $email = 'prevision@example.com'): User
    {
        return User::create([
            'nom' => 'Martin',
            'prenom' => 'Camille',
            'email' => $email,
            'mot_de_passe' => Hash::make('password'),
            'date_creation' => now(),
        ]);
    }
}
