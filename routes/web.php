<?php

use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

