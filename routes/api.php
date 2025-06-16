<?php

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrganisasiController;
use App\Http\Controllers\Api\PendaftaranController;

// Prefix: /api
use App\Http\Controllers\Api\UserController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user/update', [UserController::class, 'update']);
});

// Register & Login
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// Organisasi
Route::get('/organisasi', [OrganisasiController::class, 'index']);
Route::get('/organisasi/{id}', [OrganisasiController::class, 'show']);
Route::get('/organisasi/{id}/divisi', [OrganisasiController::class, 'divisi']);

// Pendaftaran
Route::post('/pendaftaran', [PendaftaranController::class, 'store']);
Route::get('/pendaftaran/{user_id}', [PendaftaranController::class, 'showByUserId']);
Route::put('/pendaftaran/{id}', [PendaftaranController::class, 'update']);
Route::put('/pendaftaran/seleksi/{id}', [PendaftaranController::class, 'seleksi']);

// Tes API
Route::get('/coba', function () {
    return response()->json(['pesan' => 'melayu jalan jawa pun jalan']);
});
