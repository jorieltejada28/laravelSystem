<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\AuthUsers;
use App\Http\Middleware\PreventBack;

Route::get('/', function () {
    return view('welcome');
});

// Login
Route::get('/login', [UsersController::class, 'login'])->name('login');
Route::post('/login', [UsersController::class, 'loginPost'])->name('login_post');

// Register
Route::get('/register', [UsersController::class, 'register'])->name('register');
Route::post('/register', [UsersController::class, 'registerPost'])->name('register_post');

Route::middleware([AuthUsers::class, PreventBack::class])->group(function () {

    // Terms and Condition
    Route::post('/update-terms', [UsersController::class, 'updateTerms']);

    // Dashboard
    Route::get('/dashboard', [UsersController::class, 'dashboard'])->name('dashboard');

    // Product
    Route::get('/products', [UsersController::class, 'products'])->name('products');
    Route::post('/products', [UsersController::class, 'addProduct'])->name('products.add');
    Route::put('/products/{id}', [UsersController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [UsersController::class, 'deleteProduct'])->name('products.delete');

    //Import Excel
    Route::post('/import-products', [UsersController::class, 'importExcel'])->name('import.products');

    // Export Products
    Route::get('/export-products', [UsersController::class, 'exportProducts'])->name('export.products');

    // Logout
    Route::post('/logout', [UsersController::class, 'logout'])->name('logout');

});
