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

// Product routes (public access for viewing)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])
    ->where('product', '^(?!create$|edit$|delete$)[0-9A-Za-z_-]+$')
    ->name('products.show');

// Authenticated product management routes
Route::middleware('auth')->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

// Artisan routes (public access for viewing, authenticated for management)
Route::get('/artisans', [ArtisanController::class, 'index'])->name('artisans.index');
Route::get('/artisans/{artisan}', [ArtisanController::class, 'show'])
    ->where('artisan', '^(?!create$|edit$|delete$)[0-9A-Za-z_-]+$')
    ->name('artisans.show');

// Authenticated routes with proper scoping to prevent conflicts
Route::middleware('auth')->group(function () {
    // Artisan CRUD operations with scoping
    Route::prefix('artisans')->name('artisans.')->group(function () {
        Route::get('/create', [ArtisanController::class, 'create'])->name('create');
        Route::post('/', [ArtisanController::class, 'store'])->name('store');
        Route::get('/{artisan}/edit', [ArtisanController::class, 'edit'])->name('edit');
        Route::patch('/{artisan}', [ArtisanController::class, 'update'])->name('update');
        Route::delete('/{artisan}', [ArtisanController::class, 'destroy'])->name('destroy');
    });
    
    // Comment routes with scoping
    Route::prefix('products')->name('products.')->group(function () {
        Route::post('/{product}/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::get('/{product}/comments', [CommentController::class, 'fetch'])->name('comments.fetch');
    });
    
    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

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
});

require __DIR__.'/auth.php';
