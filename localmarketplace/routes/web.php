<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

    // Routes protégées (nécessitent authentification + statut artisan)
Route::middleware(['auth'])->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'addProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'updProduct'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'delProduct'])->name('products.delete');
});
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    // Routes protégées (nécessitent authentification + statut artisan)
Route::middleware(['auth'])->group(function () {
    Route::get('/artisans/create', [ArtisanController::class, 'create'])->name('artisans.create');
    Route::post('/artisans', [ArtisanController::class, 'addArtisan'])->name('artisans.store');
    Route::get('/artisans/{artisan}/edit', [ArtisanController::class, 'edit'])->name('artisans.edit');
    Route::put('/artisans/{artisan}', [ArtisanController::class, 'updArtisan'])->name('artisans.update');
    Route::delete('/artisans/{artisan}', [ArtisanController::class, 'delArtisan'])->name('artisans.delete');    
});
Route::get('/artisans', [ArtisanController::class, 'index'])->name('artisans.index');
Route::get('/artisans/{id}', [ArtisanController::class, 'show'])->name('artisans.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




require __DIR__.'/auth.php';
