<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// --------------------------------------------------------------------------
// Public Routes
// --------------------------------------------------------------------------


Route::get('/', [HomeController::class, 'index'])->name('home');

// Catálogo de productos (con filtro de categoría por query ?categoria=rpg)
Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/productos/{product}', [ProductController::class, 'show'])->name('products.show');

// ── Rutas autenticadas (clientes) ─────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Carrito
    Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
    Route::post('/carrito/agregar', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/carrito/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/carrito/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/carrito', [CartController::class, 'clear'])->name('cart.clear');

    // AJAX — obtener cantidad total para el badge del navbar
    Route::get('/carrito/total', [CartController::class, 'total'])->name('cart.total');

    // Órdenes / Facturas
    Route::get('/mis-pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/mis-pedidos/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/mis-pedidos/{order}/cancelar', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Reseñas
    Route::post('/productos/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

});


// ── Panel de administración ───────────────────────────────────────────────────

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // CRUD de productos (resource = index, create, store, show, edit, update, destroy)
        Route::resource('productos', AdminProductController::class)
            ->parameters(['productos' => 'product']);
    });

// ── Breeze auth (login, registro, reset password, etc.) ──────────────────────
require __DIR__.'/auth.php';
