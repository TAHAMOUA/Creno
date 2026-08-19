<?php

namespace Tests\Feature;

use App\Models\Creneau;
use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreneauxDisponiblesTest extends TestCase
{
    use RefreshDatabase;

    private function createCreneau(string $date, string $heure = '10:00', int $duree = 60): Creneau
    {
        return Creneau::create([
            'date' => $date,
            'heure_debut' => $heure,
            'duree' => $duree,
        ]);
    }

    public function test_authenticated_user_can_access_available_creneaux_list(): void
    {
        $user = User::factory()->create();

        $creneau = $this->createCreneau(now()->addDay()->format('Y-m-d'));

        $response = $this->actingAs($user)
            ->get('/creneaux');

        $response->assertStatus(200);
        $response->assertViewIs('creneaux.index');
        $response->assertViewHas('creneaux');
        $response->assertSee($creneau->date->format('d/m/Y'));
        $response->assertSee($creneau->heure_debut->format('H:i'));
        $response->assertSee($creneau->duree . ' minutes');
        $response->assertSee('Réserver');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/creneaux');

        $response->assertRedirect('/login');
    }

    public function test_past_creneau_is_not_shown(): void
    {
        $user = User::factory()->create();

        $this->createCreneau(now()->subDay()->format('Y-m-d'));

        $response = $this->actingAs($user)
            ->get('/creneaux');

        $response->assertStatus(200);
        $response->assertDontSee(now()->subDay()->format('d/m/Y'));
    }

    public function test_reserved_creneau_is_not_shown(): void
    {
        $user = User::factory()->create();
        $client = User::factory()->create();

        $creneau = $this->createCreneau(now()->addDay()->format('Y-m-d'));

        RendezVous::create([
            'user_id' => $client->id,
            'creneau_id' => $creneau->id,
            'statut' => 'en_attente',
        ]);

        $response = $this->actingAs($user)
            ->get('/creneaux');

        $response->assertStatus(200);
        $response->assertDontSee($creneau->date->format('d/m/Y'));
    }

    public function test_confirmed_creneau_is_not_shown(): void
    {
        $user = User::factory()->create();
        $client = User::factory()->create();

        $creneau = $this->createCreneau(now()->addDays(2)->format('Y-m-d'));

        RendezVous::create([
            'user_id' => $client->id,
            'creneau_id' => $creneau->id,
            'statut' => 'confirme',
        ]);

        $response = $this->actingAs($user)
            ->get('/creneaux');

        $response->assertStatus(200);
        $response->assertDontSee($creneau->date->format('d/m/Y'));
    }

    public function test_cancelled_rendez_vous_creneau_is_available_again(): void
    {
        $user = User::factory()->create();
        $client = User::factory()->create();

        $creneau = $this->createCreneau(now()->addDays(3)->format('Y-m-d'));

        RendezVous::create([
            'user_id' => $client->id,
            'creneau_id' => $creneau->id,
            'statut' => 'annule',
        ]);

        $response = $this->actingAs($user)
            ->get('/creneaux');

        $response->assertStatus(200);
        $response->assertSee($creneau->date->format('d/m/Y'));
    }

    public function test_empty_state_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/creneaux');

        $response->assertStatus(200);
        $response->assertSee('Aucun créneau disponible.');
    }

    public function test_guest_cannot_reserve_creneau(): void
    {
        $creneau = $this->createCreneau(now()->addDay()->format('Y-m-d'));

        $response = $this->post('/rendez-vous', [
            'creneau_id' => $creneau->id,
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseCount('rendez_vous', 0);
    }
}