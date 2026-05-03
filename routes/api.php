<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ConferenceController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DatabaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Database Utilities (Use with caution)
Route::get('/migrate', [DatabaseController::class, 'migrate']);
Route::get('/seed', [DatabaseController::class, 'seed']);

// Settings & Home API
Route::get('/settings', [SettingController::class, 'index']);
Route::get('/home', [HomeController::class, 'index']);

// Blogs API
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{slug}', [BlogController::class, 'show']);

// Services API
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{slug}', [ServiceController::class, 'show']);

// Projects API
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{slug}', [ProjectController::class, 'show']);

// Conferences API
Route::get('/conferences', [ConferenceController::class, 'index']);
Route::get('/conferences/{slug}', [ConferenceController::class, 'show']);

// Contact Form API
Route::post('/contact', [ContactController::class, 'store']); // Новый маршрут

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
