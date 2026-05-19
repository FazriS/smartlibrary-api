<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReadingListController;

/*
|--------------------------------------------------------------------------
| Authentication (Public Routes)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

/*
|--------------------------------------------------------------------------
| Protected Routes (JWT Auth)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Auth & User
    |--------------------------------------------------------------------------
    */

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/users', [AuthController::class, 'getUsers']);

    Route::get('/users/{id}', [AuthController::class, 'getUserById']);

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */

    Route::apiResource('profiles', ProfileController::class);

    /*
    |--------------------------------------------------------------------------
    | Book Routes
    |--------------------------------------------------------------------------
    */

    Route::apiResource('books', BookController::class);

    /*
    |--------------------------------------------------------------------------
    | Admin Only Book Actions
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {
        Route::delete('/books/{id}', [BookController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Category Routes
    |--------------------------------------------------------------------------
    */

    Route::apiResource('categories', CategoryController::class);

    /*
    |--------------------------------------------------------------------------
    | Book Category Relations
    |--------------------------------------------------------------------------
    */

    Route::put('/books/{book}/categories/{category}', [CategoryController::class, 'attachCategory']);
    
    Route::get('/books/{book}/categories', [CategoryController::class, 'getBookCategories']);

    /*
    |--------------------------------------------------------------------------
    | Reading List Routes
    |--------------------------------------------------------------------------
    */

    Route::apiResource('reading-lists', ReadingListController::class);

});