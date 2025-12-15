<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KostumController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\PenyewaanController;

// LANDING PAGE
Route::get('/', function () {
    return view('welcome'); // landing page
})->name('landing');

// HANYA BOLEH DIAKSES JIKA BELUM LOGIN
Route::middleware('CheckLogin')->group(function () {
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])->name('register.process');

    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// WAJIB LOGIN
Route::middleware('CheckAuth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PILIH KOSTUM HARUS DITARUH SEBELUM RESOURCE !!!
    Route::get('/penyewaan/pilih-kostum', [PenyewaanController::class, 'select'])
        ->name('penyewaan.select');

    // RESOURCE HANYA SEKALI
    Route::resource('penyewa', PenyewaController::class);
    Route::resource('kostum', KostumController::class);
    Route::resource('penyewaan', PenyewaanController::class);
    Route::resource('pembayaran', PembayaranController::class);
    Route::resource('pengembalian', PengembalianController::class);

    Route::get('/pembayaran/{id}/nota', [PembayaranController::class, 'nota'])
        ->name('pembayaran.nota');

    Route::delete('/pengembalian/hapus/{id}', [PengembalianController::class, 'hapus'])
        ->name('pengembalian.hapus');
     Route::put('/penyewaan/{id}/update-kostum', [PenyewaanController::class, 'updateKostum'])
    ->name('penyewaan.updateKostum');

}); 


// HANYA ADMIN
Route::middleware('CheckAdmin')->group(function () {
    Route::resource('user', UserController::class);
});

// LOGOUT → KEMBALI KE LANDING
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
