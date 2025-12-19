<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/comments', [CommentController::class, 'index']);
Route::get('/comments/{comment}/replies', [CommentController::class, 'replies']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);});

    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

});
