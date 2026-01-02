<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SewaController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\KostumApiController;
use App\Http\Controllers\Api\PenyewaApiController;

Route::apiResource('sewa', SewaController::class);
Route::apiResource('kostumapi', KostumApiController::class);
Route::apiResource('penyewaapi', PenyewaApiController::class);
Route::apiResource('userapi', UserApiController::class);
