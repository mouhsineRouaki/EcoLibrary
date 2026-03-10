<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Book\BookController;
use App\Http\Controllers\Category\CategoryController;
use Illuminate\Support\Facades\Route;


Route::post('/register' , [AuthController::class , 'register']);
Route::post('/login' , [AuthController::class , 'login']);

Route::middleware('auth:sanctum')->group(function (){
    Route::post('/logout' , [AuthController::class , 'logout']);
    Route::get('/me' , [AuthController::class , 'me']);

    // Reader routes
    Route::get('/books/search', [BookController::class, 'search']);
    Route::get('/books/{id}' , [BookController::class , 'show']);
    Route::get('categories/{id}/books' , [BookController::class , 'byCategory']);
    Route::get('categories/{id}/books/popular', [BookController::class, 'popularByCategory']);
    Route::get('categories/{id}/books/new', [BookController::class, 'newByCategory']);
    Route::get('/books' , [BookController::class , 'index']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('/admin')->group(function (){
    // Admin: read all
    Route::get('/books' , [BookController::class , 'index']);
    Route::get('/categories' , [CategoryController::class , 'index']);
    Route::get('/categories/{id}' , [CategoryController::class , 'show']);

    // Admin: category CRUD
    Route::post('/categories' , [CategoryController::class , 'store']);
    Route::put('/categories/{id}' , [CategoryController::class , 'update']);
    Route::delete('/categories/{id}' , [CategoryController::class , 'destroy']);

    // Admin: book CRUD
    Route::post('/books' , [BookController::class , 'store']);
    Route::put('/books/{id}' , [BookController::class , 'update']);
    Route::delete('/books/{id}' , [BookController::class , 'destroy']);

    // Admin: stats
    Route::get('/stats/collection', [BookController::class, 'collectionStats']);
    Route::get('/stats/degraded-books', [BookController::class, 'degradedBooksStats']);
});
