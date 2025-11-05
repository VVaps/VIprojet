<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products', [ProductController::class, 'show'])->name('products.index');

    // Routes protégées (nécessitent authentification + statut artisan)
Route::middleware(['auth:sanctum', 'artisan'])->group(function () {
    Route::post('/products', [ProductController::class, 'addProduct'])->name('products.create');
    Route::put('/products', [ProductController::class, 'updProduct'])->name('products.update');
    Route::delete('/products', [ProductController::class, 'delProduct'])->name('products.update');
});
Route::get('/artisans', [ArtisanController::class, 'index'])->name('artisans.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




require __DIR__.'/auth.php';
