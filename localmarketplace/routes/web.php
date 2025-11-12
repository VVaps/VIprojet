<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

<<<<<<< HEAD
// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Authenticated product management routes
Route::middleware('auth')->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
});

=======
    // Routes protégées (nécessitent authentification + statut artisan)
Route::middleware(['auth'])->group(function () {
    Route::get('/products/create/{artisan}', [ProductController::class, 'create'])->name('products.create');
    //Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'addProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'updProduct'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'delProduct'])->name('products.delete');
});
Route::get('/products/{artisan?}', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Routes protégées (nécessitent authentification + statut artisan)
Route::middleware(['auth'])->group(function () {
    Route::get('/artisans/create', [ArtisanController::class, 'create'])->name('artisans.create');
    Route::post('/artisans', [ArtisanController::class, 'addArtisan'])->name('artisans.store');
    Route::get('/artisans/{artisan}/edit', [ArtisanController::class, 'edit'])->name('artisans.edit');
    Route::put('/artisans/{artisan}', [ArtisanController::class, 'updArtisan'])->name('artisans.update');
    Route::delete('/artisans/{artisan}', [ArtisanController::class, 'delArtisan'])->name('artisans.delete');    
});
>>>>>>> 4db345e8697054a6ed56fcfadea887a9e4ff6362
Route::get('/artisans', [ArtisanController::class, 'index'])->name('artisans.index');
Route::get('/artisans/{artisan}', [ArtisanController::class, 'show'])->name('artisans.show');

// Cart routes (accessible to both guests and authenticated users)
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::patch('/cart/{itemId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Comment routes
    Route::post('/products/{product}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/products/{product}/comments', [CommentController::class, 'fetch'])->name('comments.fetch');
    
    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});




require __DIR__.'/auth.php';
