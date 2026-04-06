<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('la orden guarda un snapshot del precio del producto al momento de la compra', function () {
    $producto = Product::factory()->create(['name' => 'Zelda', 'price' => 60.00]);
    $user = User::factory()->create();

    $order = Order::create([
        'user_id' => $user->id, 
        'status' => 'pending',
        'subtotal' => 60.00,
        'total' => 60.00
    ]);
    
    $item = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $producto->id,
        'product_name' => $producto->name, 
        'price_snapshot' => $producto->price,
        'quantity' => 1,
        'subtotal' => 60.00
    ]);

    $producto->update(['price' => 80.00]);

    // Usamos (float) para asegurar que el tipo de dato coincida con 60.00
    expect((float) $item->fresh()->price_snapshot)->toBe(60.00);
});

test('el total de la orden se calcula correctamente basado en los snapshots', function () {
    $user = User::factory()->create();
    
    // IMPORTANTE: Creamos un producto real para que exista el ID en la BD
    $producto = Product::factory()->create(); 

    $order = Order::create([
        'user_id' => $user->id,
        'status' => 'pending',
        'subtotal' => 0,
        'total' => 0
    ]);
    
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $producto->id, // Usamos el ID real del producto creado
        'product_name' => 'Juego de Prueba', 
        'price_snapshot' => 100,
        'quantity' => 2,
        'subtotal' => 200.00
    ]);

    $totalCalculado = $order->items->sum(fn($i) => $i->price_snapshot * $i->quantity);
    
    // Usamos toEqual para comparar valores numéricos sin ser estrictos con el tipo (int vs float)
    expect((float)$totalCalculado)->toBe(200.00);
});