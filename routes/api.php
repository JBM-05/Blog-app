<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')
    ->get('/check-auth', [AuthController::class, 'checkAuth']);

Route::middleware('auth:sanctum')
    ->put('/updateProfile', [AuthController::class, 'updateProfile']);
