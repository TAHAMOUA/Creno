<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\CreneauController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/rendez-vous', [RendezVousController::class, 'store'])
    ->middleware('auth')
    ->name('rendez-vous.store');

Route::patch('/rendez-vous/{rendezVous}/cancel', [RendezVousController::class, 'cancel'])
    ->middleware('auth')
    ->name('rendez-vous.cancel');
Route::get('/mes-rendez-vous', [RendezVousController::class, 'index'])
    ->middleware('auth')
    ->name('rendez-vous.index');
Route::get('/admin-test', function () {
    return 'Bienvenue Admin';
})->middleware('auth', 'isAdmin');

Route::middleware(['auth', 'isAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/creneaux', [CreneauController::class, 'index'])
            ->name('creneaux.index');

        Route::post('/creneaux', [CreneauController::class, 'store'])
            ->name('creneaux.store');

        Route::patch('/creneaux/{creneau}', [CreneauController::class, 'update'])
            ->name('creneaux.update');

        Route::delete('/creneaux/{creneau}', [CreneauController::class, 'destroy'])
            ->name('creneaux.destroy');
    });

require __DIR__.'/auth.php';