<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReadingListController;

/*
|--------------------------------------------------------------------------
| 1. Public Routes (Tanpa Login)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

/*
|--------------------------------------------------------------------------
| 2. Protected Routes (Harus Login JWT)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // --- Akses Bersama (Admin & User Bisa Melakukannya) ---
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users', [AuthController::class, 'getUsers']);
    Route::get('/users/{id}', [AuthController::class, 'getUserById']);
    
    // MENYEMPURNAKAN 405: Menambahkan akses baca detail profil secara bersama
    Route::get('/profiles/{id}', [ProfileController::class, 'show']);
    
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/books/{book}/categories', [CategoryController::class, 'getBookCategories']);


    /*
    |--------------------------------------------------------------------------
    | Otoritas KHUSUS ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware('is_admin')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{id}', [BookController::class, 'update']);
        Route::delete('/books/{id}', [BookController::class, 'destroy']);
        
        // Memperbaiki typo pendaftaran dari apiResource manual sebelumnya
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/books/{book}/category/{category}', [CategoryController::class, 'attachCategory']);
    });


    /*
    |--------------------------------------------------------------------------
    | Otoritas KHUSUS USER
    |--------------------------------------------------------------------------
    */
    Route::middleware('is_user')->group(function () {
        // Profil Mandiri (Hanya user bersangkutan yang bisa create/update)
        Route::post('/profiles', [ProfileController::class, 'store']);
        Route::put('/profiles/{id}', [ProfileController::class, 'update']);
        
        // Reading List CRUD
        Route::get('/reading-lists', [ReadingListController::class, 'index']);
        Route::post('/reading-lists', [ReadingListController::class, 'store']);
        Route::get('/reading-lists/{id}', [ReadingListController::class, 'show']);
        Route::put('/reading-lists/{id}', [ReadingListController::class, 'update']);
        Route::delete('/reading-lists/{id}', [ReadingListController::class, 'destroy']);
    });

});