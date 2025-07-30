<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'mr'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin', \App\Http\Controllers\AdminController::class . '@index')
        ->middleware('admin')
        ->name('admin.dashboard');
});

require __DIR__.'/auth.php';
