<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BooksController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return Inertia::render('welcome');
});

Route::get('/login', function () {
    return Inertia::render('login');
});

Route::get('/registration', [RoleController::class, 'index']);
Route::resource('roles', RoleController::class);

Route::middleware(['auth:api'])->group(function () {
    Route::resource('books', BooksController::class);
    Route::get('/books/{id}/show', [BooksController::class, 'show'])->name('books.show');
    Route::get('/logout', [BooksController::class, 'logout']);
});