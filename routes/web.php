<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KostumController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenyewaanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengembalianController;

/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('landing');

/*
|--------------------------------------------------------------------------
| GUEST ONLY (BELUM LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('CheckLogin')->group(function () {
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])->name('register.process');

    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

/*
|--------------------------------------------------------------------------
| PENYEWA (LOGIN + ROLE PENYEWA)
|--------------------------------------------------------------------------
| Route khusus sebelum resource penyewaan
*/
Route::middleware(['CheckAuth', 'CheckPenyewa'])->group(function () {
    Route::get('/penyewaan/pilih-kostum', [PenyewaanController::class, 'select'])
        ->name('penyewaan.select');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('CheckAuth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'ajaxData'])->name('dashboard.data');

    // Master & Transaksi
    Route::resource('penyewa', PenyewaController::class);
    Route::resource('kostum', KostumController::class);
    Route::resource('penyewaan', PenyewaanController::class);
    Route::resource('pembayaran', PembayaranController::class);
    Route::resource('pengembalian', PengembalianController::class);

    // Route tambahan transaksi
    Route::get('/pembayaran/{id}/nota', [PembayaranController::class, 'nota'])
        ->name('pembayaran.nota');

    Route::delete('/pengembalian/hapus/{id}', [PengembalianController::class, 'hapus'])
        ->name('pengembalian.hapus');

    Route::put('/penyewaan/{id}/update-kostum', [PenyewaanController::class, 'updateKostum'])
        ->name('penyewaan.updateKostum');

    // PROFILE (AJAX FRIENDLY, TANPA PARAMETER)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'photo'])->name('profile.photo');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/
Route::middleware('CheckAdmin')->group(function () {
    Route::resource('user', UserController::class);
});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
