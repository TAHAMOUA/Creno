<?php

namespace Database\Factories;

use App\Models\RendezVous;
use Illuminate\Database\Eloquent\Factories\Factory;

class RendezVousFactory extends Factory
{
    protected $model = RendezVous::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'creneau_id' => 1,
            'statut' => 'en_attente',
        ];
    }
}