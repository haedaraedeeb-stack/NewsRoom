<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function (){
Route::post('/register', [AuthController::class, 'register'])
    ->name('register');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');

Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('articles/{articleId}/comments', [CommentController::class, 'index'])->name('comments.index');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
    Route::patch('articles/{id}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
    Route::patch('articles/{id}/archive', [ArticleController::class, 'archive'])->name('articles.archive');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::post('articles/{articleId}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });
});
