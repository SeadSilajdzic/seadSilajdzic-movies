<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * ===== Authentication routes =====
*/
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
Route::post('register', [AuthController::class, 'register'])->name('register');


Route::middleware('auth')->group(function () {
    /**
     * ===== Movies routes =====
     */
    Route::get('movies/search/{string}', [MovieController::class, 'search'])->name('movies.search');
    Route::post('movies/favourite/{movie}', [MovieController::class, 'favourite'])->name('movies.favourite');
    Route::get('movies/cache-favourite-list', [MovieController::class, 'cached'])->name('movies.cached');
    Route::post('movies/cache-favourite-list/{timeInSeconds?}', [MovieController::class, 'cache'])->name('movies.cache');
    Route::resource('movies', MovieController::class)->except(['edit', 'create']);

    // Other routes ...
});
