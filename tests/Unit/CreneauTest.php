<?php

namespace Tests\Unit;

use App\Models\Creneau;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreneauTest extends TestCase
{
    use RefreshDatabase;

    public function test_creneaux_se_chevauchent(): void
    {
        $creneau1 = new Creneau([
            'date' => '2026-08-20',
            'heure_debut' => '10:00',
            'duree' => 60,
        ]);

        $creneau2 = new Creneau([
            'date' => '2026-08-20',
            'heure_debut' => '10:30',
            'duree' => 60,
        ]);

        $this->assertTrue($creneau1->chevauche($creneau2));
    }

    public function test_creneaux_a_la_frontiere_ne_se_chevauchent_pas(): void
    {
        $creneau1 = new Creneau([
            'date' => '2026-08-20',
            'heure_debut' => '10:00',
            'duree' => 60,
        ]);

        $creneau2 = new Creneau([
            'date' => '2026-08-20',
            'heure_debut' => '11:00',
            'duree' => 60,
        ]);

        $this->assertFalse($creneau1->chevauche($creneau2));
    }

    public function test_creneaux_de_jours_differents_ne_se_chevauchent_pas(): void
    {
        $creneau1 = new Creneau([
            'date' => '2026-08-20',
            'heure_debut' => '10:00',
            'duree' => 60,
        ]);

        $creneau2 = new Creneau([
            'date' => '2026-08-21',
            'heure_debut' => '10:00',
            'duree' => 60,
        ]);

        $this->assertFalse($creneau1->chevauche($creneau2));
    }

    public function test_creneau_passe_est_detecte(): void
    {
        $creneau = Creneau::create([
            'date' => now()->subDay()->format('Y-m-d'),
            'heure_debut' => '10:00',
            'duree' => 60,
        ]);

        $this->assertTrue(
            Creneau::passes()->whereKey($creneau->id)->exists()
        );
    }
}