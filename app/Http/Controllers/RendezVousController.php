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

   

    $rendezVous = RendezVous::create([
        'user_id' => auth()->id(),
        'creneau_id' => $creneau->id,
        'statut' => 'en_attente',
    ]);

    return redirect()->back()->with(
        'success',
        'Rendez-vous réservé avec succès.'
    );
}
}
