<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Movies\MovieCacheController;
use App\Http\Controllers\Movies\MovieController;
use App\Http\Controllers\Movies\MovieFavouriteListController;
use App\Http\Controllers\Movies\MovieSearchController;
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
    // Search routes
    Route::get('movies/search/{string}', [MovieSearchController::class, 'search'])->name('movies.search');
    // Favourite list routes
    Route::post('movies/favourite/{movie}', [MovieFavouriteListController::class, 'favourite'])->name('movies.favourite');
    // Cache routes
    Route::get('movies/cache-favourite-list', [MovieCacheController::class, 'cached'])->name('movies.cached');
    Route::post('movies/cache-favourite-list/{timeInSeconds?}', [MovieCacheController::class, 'cache'])->name('movies.cache');
    // Movie resource routes
    Route::resource('movies', MovieController::class)->except(['edit', 'create']);


    /**
     * ===== Other models routes ... =====
     */
});
