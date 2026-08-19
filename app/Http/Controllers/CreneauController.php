<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCreneauRequest;
use App\Http\Requests\UpdateCreneauRequest;
use App\Models\Creneau;

class CreneauController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $creneaux = Creneau::latest('date')
        ->latest('heure_debut')
        ->get();

    return view('admin.creneaux.index', compact('creneaux'));
}

    public function disponibles()
    {
        $creneaux = Creneau::disponibles()
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();

        return view('creneaux.index', compact('creneaux'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCreneauRequest $request)
  {
    Creneau::create($request->validated());

    return redirect()
        ->route('admin.creneaux.index')
        ->with('success', 'Créneau créé avec succès.');
   }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCreneauRequest $request, Creneau $creneau)
    {
    $creneau->update($request->validated());

    return back()->with(
        'success',
        'Créneau modifié avec succès.'
    );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Creneau $creneau)
    {
    if ($creneau->rendezVous()->exists()) {
        return back()->withErrors([
            'creneau' => 'Impossible de supprimer ce créneau car il contient des rendez-vous.',
        ]);
    }

    $creneau->delete();

    return back()->with(
        'success',
        'Créneau supprimé avec succès.'
    );
    }
}
