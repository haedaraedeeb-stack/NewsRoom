<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\V1\ArticleController as ArticleControllerV1;
use App\Http\Controllers\V2\ArticleController as ArticleControllerV2;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function (){
Route::post('/register', [AuthController::class, 'register'])
    ->name('register');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login');
Route::get('articles/{articleId}/comments', [CommentController::class, 'index'])->name('comments.index');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::post('articles/{articleId}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    // V1
    Route::prefix('v1')->name('v1.')->group(function () {
        Route::patch('articles/{id}/publish', [ArticleControllerV1::class, 'publish'])->name('articles.publish');
        Route::patch('articles/{id}/archive', [ArticleControllerV1::class, 'archive'])->name('articles.archive');
        Route::apiResource('articles', ArticleControllerV1::class);
    });

    // V2
    Route::prefix('v2')->name('v2.')->group(function () {
        Route::patch('articles/{id}/publish', [ArticleControllerV2::class, 'publish'])->name('articles.publish');
        Route::patch('articles/{id}/archive', [ArticleControllerV2::class, 'archive'])->name('articles.archive');
        Route::apiResource('articles', ArticleControllerV2::class);
        });
    });
});
