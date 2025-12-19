<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')
    ->get('/check-auth', [AuthController::class, 'checkAuth']);

Route::middleware('auth:sanctum')
    ->put('/updateProfile', [AuthController::class, 'updateProfile']);
    Route::middleware('auth:sanctum')
    ->get('/getCategories', [CategoryController::class, 'index']);
Route::middleware(['auth:sanctum', 'admin'])
    ->post('/addCategory', [CategoryController::class, 'store']);
Route::middleware(['auth:sanctum', 'admin'])
    ->delete('/deleteCategory', [CategoryController::class, 'destroy']);
Route::middleware('auth:sanctum')
    ->post('/createPost', [PostController::class, 'store']);
Route::middleware('auth:sanctum')
    ->get('/getPosts', [PostController::class, 'show']);
Route::middleware('auth:sanctum')
    ->put('/posts/{post}', [PostController::class, 'update']);
Route::middleware('auth:sanctum')
    ->delete('/posts/{post}', [PostController::class, 'destroy']);
