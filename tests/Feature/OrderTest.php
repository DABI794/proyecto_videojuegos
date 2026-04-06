<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('la orden guarda un snapshot del precio del producto al momento de la compra', function () {
    // REFACTORIZADO: Nombres descriptivos para mejor legibilidad
    $productToPurchase = Product::factory()->create(['name' => 'Zelda', 'price' => 60.00]);
    $authenticatedUser = User::factory()->create();

    $order = Order::create([
        'user_id' => $authenticatedUser->id, 
        'status' => 'pending',
        'subtotal' => 60.00,
        'total' => 60.00
    ]);
    
    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $productToPurchase->id,
        'product_name' => $productToPurchase->name, 
        'price_snapshot' => $productToPurchase->price,
        'quantity' => 1,
        'subtotal' => 60.00
    ]);

    $productToPurchase->update(['price' => 80.00]);

    // Verificamos que el snapshot sea inmutable
    expect((float) $orderItem->fresh()->price_snapshot)->toBe(60.00);
});

test('el total de la orden se calcula correctamente basado en los snapshots', function () {
    $authenticatedUser = User::factory()->create();
    $productForTotalTest = Product::factory()->create(); 

    $order = Order::create([
        'user_id' => $authenticatedUser->id,
        'status' => 'pending',
        'subtotal' => 0,
        'total' => 0
    ]);
    
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $productForTotalTest->id,
        'product_name' => 'Juego de Prueba', 
        'price_snapshot' => 100.00,
        'quantity' => 2,
        'subtotal' => 200.00
    ]);

    $totalCalculado = $order->items->sum(fn($item) => $item->price_snapshot * $item->quantity);
    
    expect((float)$totalCalculado)->toBe(200.00);
});