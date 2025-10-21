<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\CustomerReviewsController;
use Illuminate\Support\Facades\Auth;

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

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::middleware(['auth:api'])->group(function () {
            Route::get('logout', [AuthController::class, 'logout']);
            Route::post('refresh_token', [AuthController::class, 'refresh_token']);
            Route::get('get_user', [AuthController::class, 'get_user']);
            Route::post('book_search', [BooksController::class, 'search']);
            // Same function for update and create.
            Route::post('book_create', [BooksController::class, 'create_request']);
            Route::post('book_edit', [BooksController::class, 'create_request']);
            // Add reviews
            Route::post('review_create', [CustomerReviewsController::class, 'review_create']);
            //Mark checkout
            Route::post('book_checkout', [BooksController::class, 'mark_checkout']);
            // Mark return
            Route::post('book_return', [BooksController::class, 'mark_as_returned']);
            // Delete book
            Route::post('book_delete', [BooksController::class, 'destroy']);
        });
    });
});

Route::get( '/', [ AuthController::class, 'unauthenticated' ] )->name( 'login' );