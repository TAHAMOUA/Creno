<?php

namespace Tests\Feature;

use App\Models\Creneau;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreneauManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_creneau(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $creneau = Creneau::create([
            'date' => now()->addDay()->format('Y-m-d'),
            'heure_debut' => '10:00:00',
            'duree' => 30,
        ]);

        $response = $this->actingAs($admin)
            ->delete("/admin/creneaux/{$creneau->id}");

        $response->assertRedirect();

        $this->assertDatabaseMissing('creneaux', [
            'id' => $creneau->id,
        ]);
    }public function test_client_cannot_delete_creneau(): void
{
    $client = User::factory()->create([
        'role' => 'client',
    ]);

    $creneau = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);

    $response = $this->actingAs($client)
        ->delete("/admin/creneaux/{$creneau->id}");

    $response->assertForbidden();

    $this->assertDatabaseHas('creneaux', [
        'id' => $creneau->id,
    ]);
}
public function test_guest_cannot_delete_creneau(): void
{
    $creneau = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);

    $response = $this->delete("/admin/creneaux/{$creneau->id}");

    $response->assertRedirect('/login');

    $this->assertDatabaseHas('creneaux', [
        'id' => $creneau->id,
    ]);
}
public function test_admin_cannot_delete_creneau_with_rendez_vous(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $client = User::factory()->create([
        'role' => 'client',
    ]);

    $creneau = Creneau::create([
        'date' => now()->addDay()->format('Y-m-d'),
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);

    $rendezVous = \App\Models\RendezVous::create([
        'user_id' => $client->id,
        'creneau_id' => $creneau->id,
        'statut' => 'confirme',
    ]);

    $response = $this->actingAs($admin)
        ->delete("/admin/creneaux/{$creneau->id}");

    $response->assertRedirect();

    $this->assertDatabaseHas('creneaux', [
        'id' => $creneau->id,
    ]);
}
public function test_admin_can_create_creneau(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $date = now()->addDay()->format('Y-m-d');

    $response = $this->actingAs($admin)
        ->post('/admin/creneaux', [
            'date' => $date,
            'heure_debut' => '14:00',
            'duree' => 45,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('creneaux', [
        'date' => $date . ' 00:00:00',
        'heure_debut' => '14:00',
        'duree' => 45,
    ]);
}
public function test_client_cannot_create_creneau(): void
{
    $client = User::factory()->create([
        'role' => 'client',
    ]);

    $response = $this->actingAs($client)
        ->post('/admin/creneaux', [
            'date' => '2026-08-26',
            'heure_debut' => '15:00',
            'duree' => 30,
        ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('creneaux', [
        'date' => '2026-08-26 00:00:00',
        'heure_debut' => '15:00',
        'duree' => 30,
    ]);
}
public function test_guest_cannot_create_creneau(): void
{
    $response = $this->post('/admin/creneaux', [
        'date' => '2026-08-27',
        'heure_debut' => '16:00',
        'duree' => 30,
    ]);

    $response->assertRedirect('/login');

    $this->assertDatabaseMissing('creneaux', [
        'date' => '2026-08-27 00:00:00',
        'heure_debut' => '16:00',
        'duree' => 30,
    ]);
}
public function test_admin_can_update_creneau(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $creneau = Creneau::create([
        'date' => '2026-08-28',
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);

    $response = $this->actingAs($admin)
        ->patch("/admin/creneaux/{$creneau->id}", [
            'date' => '2026-08-29',
            'heure_debut' => '14:00',
            'duree' => 60,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('creneaux', [
        'id' => $creneau->id,
        'date' => '2026-08-29 00:00:00',
        'heure_debut' => '14:00',
        'duree' => 60,
    ]);
}
public function test_client_cannot_update_creneau(): void
{
    $client = User::factory()->create([
        'role' => 'client',
    ]);

    $creneau = Creneau::create([
        'date' => '2026-08-30',
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);

    $response = $this->actingAs($client)
        ->patch("/admin/creneaux/{$creneau->id}", [
            'date' => '2026-08-31',
            'heure_debut' => '15:00',
            'duree' => 60,
        ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('creneaux', [
        'id' => $creneau->id,
        'date' => '2026-08-30 00:00:00',
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);
}
public function test_guest_cannot_update_creneau(): void
{
    $creneau = Creneau::create([
        'date' => '2026-09-01',
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);

    $response = $this->patch("/admin/creneaux/{$creneau->id}", [
        'date' => '2026-09-02',
        'heure_debut' => '15:00',
        'duree' => 60,
    ]);

    $response->assertRedirect('/login');

    $this->assertDatabaseHas('creneaux', [
        'id' => $creneau->id,
        'date' => '2026-09-01 00:00:00',
        'heure_debut' => '10:00:00',
        'duree' => 30,
    ]);
}
public function test_admin_cannot_create_creneau_without_date(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)
        ->post('/admin/creneaux', [
            'heure_debut' => '10:00',
            'duree' => 30,
        ]);

    $response->assertSessionHasErrors('date');

    $this->assertDatabaseCount('creneaux', 0);
}
public function test_admin_cannot_create_creneau_with_invalid_date(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)
        ->post('/admin/creneaux', [
            'date' => 'not-a-date',
            'heure_debut' => '10:00',
            'duree' => 30,
        ]);

    $response->assertSessionHasErrors('date');

    $this->assertDatabaseCount('creneaux', 0);
}
public function test_admin_cannot_create_creneau_without_heure_debut(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)
        ->post('/admin/creneaux', [
            'date' => '2026-09-05',
            'duree' => 30,
        ]);

    $response->assertSessionHasErrors('heure_debut');

    $this->assertDatabaseCount('creneaux', 0);
}
}