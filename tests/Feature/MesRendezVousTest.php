<?php

namespace Tests\Feature;

use App\Models\Creneau;
use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MesRendezVousTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_see_their_rendez_vous(): void
    {
        $user = User::factory()->create();

        $creneau = Creneau::create([
            'date' => now()->addDay()->format('Y-m-d'),
            'heure_debut' => '10:00',
            'duree' => 60,
        ]);

        RendezVous::create([
            'user_id' => $user->id,
            'creneau_id' => $creneau->id,
            'statut' => 'en_attente',
        ]);

        $response = $this->actingAs($user)
            ->get('/mes-rendez-vous');

        $response->assertStatus(200);
        $response->assertViewIs('rendez-vous.index');
        $response->assertViewHas('rendezVous');
    }
    public function test_guest_cannot_see_rendez_vous(): void
    {
    $response = $this->get('/mes-rendez-vous');

    $response->assertRedirect('/login');
    }
   public function test_client_can_only_see_their_own_rendez_vous(): void
    {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $creneau1 = Creneau::create([
        'date' => now()->addDays(1)->format('Y-m-d'),
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);

    $creneau2 = Creneau::create([
        'date' => now()->addDays(1)->format('Y-m-d'),
        'heure_debut' => '11:00:00',
        'duree' => 30,
    ]);

    $rendezVous1 = RendezVous::factory()->create([
        'user_id' => $user1->id,
        'creneau_id' => $creneau1->id,
    ]);

    $rendezVous2 = RendezVous::factory()->create([
        'user_id' => $user2->id,
        'creneau_id' => $creneau2->id,
    ]);

    $response = $this->actingAs($user1)
        ->get('/mes-rendez-vous');

    $response->assertStatus(200);

    $response->assertViewHas('rendezVous', function ($rendezVous) use ($rendezVous1, $rendezVous2) {
        return $rendezVous->contains($rendezVous1)
            && !$rendezVous->contains($rendezVous2);
    });
    }
}