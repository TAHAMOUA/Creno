<?php

namespace App\Providers;

use App\Models\RendezVous;
use App\Policies\RendezVousPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(RendezVous::class, RendezVousPolicy::class);
    }
}