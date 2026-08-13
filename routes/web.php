<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RendezVousController;

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
Route::get('/admin-test', function () {
    return 'Bienvenue Admin';
})->middleware('auth', 'isAdmin');
Route::post('/rendez-vous', [RendezVousController::class, 'store'])
    ->middleware('auth')
    ->name('rendez-vous.store');
require __DIR__.'/auth.php';
Route::patch('/rendez-vous/{rendezVous}/cancel', [RendezVousController::class, 'cancel'])
    ->middleware('auth')
    ->name('rendez-vous.cancel');
