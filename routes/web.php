<?php

use App\Http\Controllers\Manager\BorrowerController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::inertia('mentions-legales', 'public/Legal')->name('legal');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::inertia('a-propos', 'public/About')->name('about');

// --- Routes Emprunteurs (Maurin) ---
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/borrowers', [BorrowerController::class, 'index'])->name('borrowers.index');
    Route::post('/borrowers', [BorrowerController::class, 'store'])->name('borrowers.store');
});
require __DIR__.'/settings.php';
