<?php

namespace Tests\Feature\Api;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_and_mark_own_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $notification = Notification::create([
            'id_utilisateur' => $user->id_utilisateur,
            'type' => 'depense_prevision_validee',
            'titre' => 'Prévision validée',
            'contenu' => 'Votre dépense a été enregistrée.',
        ]);
        Notification::create([
            'id_utilisateur' => User::factory()->create()->id_utilisateur,
            'type' => 'revenu_prevision_percu',
            'titre' => 'Notification privée',
            'contenu' => 'Elle ne doit pas être exposée.',
        ]);
        Notification::create([
            'id_utilisateur' => User::factory()->create()->id_utilisateur,
            'type' => 'depense_prevision_validee',
            'titre' => 'Notification privée',
            'contenu' => 'Elle ne doit pas être exposée.',
        ]);

        $this->actingAs($user)
            ->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('unread_count', 1);

        $this->patchJson("/api/notifications/{$notification->id_notification}/read")
            ->assertOk()
            ->assertJsonPath('data.est_lue', true);
    }

    public function test_user_cannot_access_or_delete_another_users_notification(): void
    {
        $user = User::factory()->create();
        $notification = Notification::create([
            'id_utilisateur' => User::factory()->create()->id_utilisateur,
            'type' => 'revenu_prevision_percu',
            'titre' => 'Revenu perçu',
            'contenu' => 'Votre revenu a été enregistré.',
        ]);

        $this->actingAs($user)->getJson("/api/notifications/{$notification->id_notification}")->assertForbidden();
        $this->deleteJson("/api/notifications/{$notification->id_notification}")->assertForbidden();
    }

    public function test_user_can_mark_all_notifications_as_read_and_delete_one(): void
    {
        $user = User::factory()->create();
        $notification = Notification::create([
            'id_utilisateur' => $user->id_utilisateur,
            'type' => 'revenu_prevision_percu',
            'titre' => 'Revenu perçu',
            'contenu' => 'Votre revenu a été enregistré.',
        ]);

        $this->actingAs($user)->patchJson('/api/notifications/read-all')->assertOk();
        $this->assertDatabaseHas('notifications', ['id_notification' => $notification->id_notification, 'est_lue' => true]);

        $this->deleteJson("/api/notifications/{$notification->id_notification}")->assertOk();
        $this->assertDatabaseMissing('notifications', ['id_notification' => $notification->id_notification]);
    }
}
