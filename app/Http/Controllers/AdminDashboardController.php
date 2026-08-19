<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $rendezVous = RendezVous::with(['user', 'creneau'])
            ->latest()
            ->get();

        return view('admin.dashboard', compact('rendezVous'));
    }
}