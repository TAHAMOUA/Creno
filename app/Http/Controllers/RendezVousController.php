<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRendezVousRequest;
use App\Models\RendezVous;
use App\Models\Creneau;

class RendezVousController extends Controller
{
    public function store(StoreRendezVousRequest $request)
    {
        $creneau = Creneau::findOrFail($request->creneau_id);

        // 1. Créneau passé
        if (Creneau::query()
            ->whereKey($creneau->id)
            ->passes()
            ->exists()) {
            return back()->withErrors([
                'creneau_id' => 'Ce créneau est déjà passé.',
            ]);
        }

        // 2. Créneau déjà réservé
        if ($creneau->rendezVous()
            ->whereIn('statut', ['en_attente', 'confirme'])
            ->exists()) {
            return back()->withErrors([
                'creneau_id' => 'Ce créneau est déjà réservé.',
            ]);
        }

        // 3. Chevauchement avec un autre rendez-vous du client
        $userRendezVous = auth()->user()
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

        // 4. Création du rendez-vous
        RendezVous::create([
            'user_id' => auth()->id(),
            'creneau_id' => $creneau->id,
            'statut' => 'en_attente',
        ]);

        return back()->with('success', 'Rendez-vous réservé avec succès.');
    }
    public function cancel(RendezVous $rendezVous)
{
    $this->authorize('delete', $rendezVous);

    $rendezVous->update([
        'statut' => 'annule',
    ]);

    return back()->with(
        'success',
        'Rendez-vous annulé avec succès.'
    );
}
}