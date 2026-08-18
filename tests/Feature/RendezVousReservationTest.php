<?php

namespace Tests\Feature;

use App\Models\Creneau;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RendezVousReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_reserve_available_creneau(): void
    {
        $user = User::factory()->create();

        $creneau = Creneau::create([
            'date' => now()->addDay()->format('Y-m-d'),
            'heure_debut' => '10:00',
            'duree' => 60,
        ]);

        $response = $this->actingAs($user)
            ->post('/rendez-vous', [
                'creneau_id' => $creneau->id,
            ]);
        $response->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('rendez_vous', [
            'user_id' => $user->id,
            'creneau_id' => $creneau->id,
            'statut' => 'en_attente',
        ]);
    }
    public function test_client_cannot_reserve_already_reserved_creneau(): void
{
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $creneau = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00',
        'duree' => 60,
    ]);

    // Le premier client réserve le créneau
    $this->actingAs($user1)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau->id,
        ]);

    // Le deuxième client essaie de réserver le même créneau
    $response = $this->actingAs($user2)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau->id,
        ]);

    $response->assertSessionHasErrors('creneau_id');

    $this->assertDatabaseCount('rendez_vous', 1);
    }
    public function test_client_cannot_reserve_past_creneau(): void
{
    $user = User::factory()->create();

    $creneau = Creneau::create([
        'date' => now()->subDay()->format('Y-m-d'),
        'heure_debut' => '10:00',
        'duree' => 60,
    ]);

    $response = $this->actingAs($user)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau->id,
        ]);

    $response->assertSessionHasErrors('creneau_id');

    $this->assertDatabaseCount('rendez_vous', 0);
    }
    public function test_client_cannot_reserve_overlapping_creneau(): void
{
    $user = User::factory()->create();

    $creneau1 = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00',
        'duree' => 60,
    ]);

    $creneau2 = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:30',
        'duree' => 60,
    ]);

    // Premier rendez-vous
    $this->actingAs($user)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau1->id,
        ]);

    // Deuxième créneau chevauche le premier
    $response = $this->actingAs($user)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau2->id,
        ]);

    $response->assertSessionHasErrors('creneau_id');

    // Un seul rendez-vous doit exister
    $this->assertDatabaseCount('rendez_vous', 1);
    }
    public function test_guest_cannot_reserve_creneau(): void
{
    $creneau = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00',
        'duree' => 60,
    ]);

    $response = $this->post('/rendez-vous', [
        'creneau_id' => $creneau->id,
    ]);

    $response->assertRedirect('/login');

    $this->assertDatabaseCount('rendez_vous', 0);
    }
    public function test_client_can_reserve_creneau_after_previous_reservation_is_cancelled(): void
{
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $creneau = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00',
        'duree' => 60,
    ]);

    // Client 1 réserve
    $this->actingAs($user1)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau->id,
        ]);

    $rendezVous = \App\Models\RendezVous::first();

    // Client 1 annule
    $this->actingAs($user1)
        ->patch("/rendez-vous/{$rendezVous->id}/cancel");

    // Client 2 réserve نفس créneau
    $response = $this->actingAs($user2)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau->id,
        ]);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('rendez_vous', [
        'user_id' => $user2->id,
        'creneau_id' => $creneau->id,
        'statut' => 'en_attente',
    ]);
    }
    public function test_client_cannot_reserve_same_creneau_twice(): void
{
    $user = User::factory()->create();

    $creneau = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00',
        'duree' => 60,
    ]);

    // Premier réservation
    $this->actingAs($user)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau->id,
        ]);

    $response = $this->actingAs($user)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau->id,
        ]);

    $response->assertSessionHasErrors('creneau_id');

    $this->assertDatabaseCount('rendez_vous', 1);
    }
    public function test_client_can_reserve_creneau_starting_when_previous_one_ends(): void
{
    $user = User::factory()->create();

    $creneau1 = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00',
        'duree' => 60,
    ]);

    $creneau2 = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '11:00',
        'duree' => 60,
    ]);

    // Premier rendez-vous : 10h → 11h
    $this->actingAs($user)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau1->id,
        ]);

    // Deuxième rendez-vous : 11h → 12h
    $response = $this->actingAs($user)
        ->post('/rendez-vous', [
            'creneau_id' => $creneau2->id,
        ]);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseCount('rendez_vous', 2);

    $this->assertDatabaseHas('rendez_vous', [
        'user_id' => $user->id,
        'creneau_id' => $creneau2->id,
        'statut' => 'en_attente',
    ]);
    }
}