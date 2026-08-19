<?php

namespace Tests\Feature;

use App\Models\Creneau;
use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function createRendezVous(string $statut, User $client): RendezVous
    {
        $creneau = Creneau::create([
            'date' => now()->addDay()->format('Y-m-d'),
            'heure_debut' => '10:00',
            'duree' => 60,
        ]);

        return RendezVous::create([
            'user_id' => $client->id,
            'creneau_id' => $creneau->id,
            'statut' => $statut,
        ]);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('rendezVous');
    }

    public function test_client_cannot_access_dashboard(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)
            ->get('/admin');

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_displays_client_creneau_and_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['name' => 'Client Exemple']);

        $rendezVous = $this->createRendezVous('en_attente', $client);

        $response = $this->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Client Exemple');
        $response->assertSee($rendezVous->creneau->date->format('d/m/Y'));
        $response->assertSee($rendezVous->creneau->heure_debut->format('H:i'));
        $response->assertSee('En attente');
    }

    public function test_cancelled_rendez_vous_is_distinguishable(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['name' => 'Client Annule']);

        $this->createRendezVous('annule', $client);
        $this->createRendezVous('confirme', $client);

        $response = $this->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Annulé');
        $response->assertSee('Confirmé');
    }

    public function test_empty_state_is_displayed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Aucun rendez-vous.');
    }
}