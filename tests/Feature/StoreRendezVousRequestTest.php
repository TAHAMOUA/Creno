<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class StoreRendezVousRequestTest extends TestCase
{
    use RefreshDatabase;
    public function test_creneau_id_is_required(): void
{
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/rendez-vous', []);

    $response->assertSessionHasErrors('creneau_id');
}
public function test_creneau_id_must_be_an_integer(): void
{
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/rendez-vous', [
            'creneau_id' => 'abc',
        ]);

    $response->assertSessionHasErrors('creneau_id');
}
public function test_creneau_id_must_exist(): void
{
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/rendez-vous', [
            'creneau_id' => 999999,
        ]);

    $response->assertSessionHasErrors('creneau_id');
}
}