<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRendezVousRequest;
use App\Models\RendezVous;
use App\Models\Creneau;
use Illuminate\Support\Facades\DB;

class RendezVousController extends Controller
{
    public function store(StoreRendezVousRequest $request)
    {
    $user = $request->user();

    return DB::transaction(function () use ($request, $user) {

        $creneau = Creneau::query()
            ->whereKey($request->creneau_id)
            ->lockForUpdate()
            ->firstOrFail();

        // 1. Créneau passé
        if (
            Creneau::query()
                ->whereKey($creneau->id)
                ->passes()
                ->exists()
        ) {
            return back()->withErrors([
                'creneau_id' => 'Ce créneau est déjà passé.',
            ]);
        }

        // 2. Créneau déjà réservé
        if (
            $creneau->rendezVous()
                ->whereIn('statut', ['en_attente', 'confirme'])
                ->exists()
        ) {
            return back()->withErrors([
                'creneau_id' => 'Ce créneau est déjà réservé.',
            ]);
        }

        // 3. Chevauchement avec un autre rendez-vous du client
        $userRendezVous = $user
            ->rendezVous()
            ->whereIn('statut', ['en_attente', 'confirme'])
            ->with('creneau')
            ->get();

        foreach ($userRendezVous as $rendezVous) {
            if ($creneau->chevauche($rendezVous->creneau)) {
                return back()->withErrors([
                    'creneau_id' => 'Ce créneau chevauche un autre rendez-vous.',
                ]);
            }
        }

        // 4. Création
        RendezVous::create([
            'user_id' => $user->id,
            'creneau_id' => $creneau->id,
            'statut' => 'en_attente',
        ]);

        return back()->with(
            'success',
            'Rendez-vous réservé avec succès.'
        );
    });
    }
    public function index()
    {
    $rendezVous = auth()->user()
        ->rendezVous()
        ->with('creneau')
        ->latest()
        ->get();

    return view('rendez-vous.index', compact('rendezVous'));
    }
   public function cancel(RendezVous $rendezVous)
{
    $user = auth()->user();

    if (! app(\App\Policies\RendezVousPolicy::class)
        ->delete($user, $rendezVous)) {
        abort(403);
    }

    $rendezVous->update([
        'statut' => 'annule',
    ]);

    return back()->with(
        'success',
        'Rendez-vous annulé avec succès.'
    );
}
}