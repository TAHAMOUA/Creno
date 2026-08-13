<?php

namespace Tests\Feature;

use App\Models\RendezVous;
use App\Models\User;
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
}