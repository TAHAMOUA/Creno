<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IsAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_route(): void
    {
        $response = $this->get('/admin-test');

        $response->assertRedirect('/login');
    }

    public function test_client_cannot_access_admin_route(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
        ]);

        $response = $this->actingAs($user)
            ->get('/admin-test');

        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_route(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin-test');

        $response->assertOk();
        $response->assertSee('Bienvenue Admin');
    }
}