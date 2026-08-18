<?php

namespace Tests\Feature;

use App\Models\RendezVous;
use App\Models\User;
use App\Models\Creneau;
use Tests\TestCase;
use App\Policies\RendezVousPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
class RendezVousPolicyTest extends TestCase
{
    use RefreshDatabase;
    public function test_client_can_cancel_their_own_rendez_vous(): void
    {
        $user = User::factory()->make();

        $rendezVous = new RendezVous([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(
            $user->can('delete', $rendezVous)
        );
    }

    public function test_client_cannot_cancel_another_client_rendez_vous(): void
{
    $user = User::factory()->create();

    $otherUser = User::factory()->create();

    $rendezVous = new RendezVous([
        'user_id' => $otherUser->id,
    ]);

    $this->assertFalse(
        app(RendezVousPolicy::class)->delete($user, $rendezVous)
    );
}
public function test_guest_cannot_cancel_rendez_vous(): void
{
    $user = User::factory()->create();

    $creneau = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);

    $rendezVous = RendezVous::factory()->create([
        'user_id' => $user->id,
        'creneau_id' => $creneau->id,
        'statut' => 'en_attente',
    ]);

    $response = $this->patch(
        "/rendez-vous/{$rendezVous->id}/cancel"
    );

    $response->assertRedirect('/login');
}
public function test_client_can_cancel_rendez_vous_and_status_becomes_annule(): void
{
    $user = User::factory()->create();

    $creneau = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);

    $rendezVous = RendezVous::factory()->create([
        'user_id' => $user->id,
        'creneau_id' => $creneau->id,
        'statut' => 'en_attente',
    ]);

    $response = $this->actingAs($user)
        ->patch("/rendez-vous/{$rendezVous->id}/cancel");

    $response->assertRedirect();

    $this->assertDatabaseHas('rendez_vous', [
        'id' => $rendezVous->id,
        'statut' => 'annule',
    ]);
}
}