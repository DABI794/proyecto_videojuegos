<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Historial de pedidos del usuario.
     */
    public function index(): View
    {
        $orders = auth()->user()
            ->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Detalle de una orden / factura — reemplaza factura.php
     */
    public function show(Order $order): View
    {
        // Solo el dueño o un admin puede ver la orden
        abort_if(
            $order->user_id !== auth()->id() && ! auth()->user()->isAdmin(),
            403
        );

        $order->load('items.product', 'user');

        return view('orders.show', compact('order'));
    }

    /**
     * Crear orden desde el carrito (checkout).
     */
    public function store(Request $request): RedirectResponse
    {
        $user      = auth()->user();
        $cartItems = $user->cartItems()->with('product')->get();

        // Validar que el carrito no esté vacío
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        // Validar stock de todos los productos antes de proceder
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "Stock insuficiente para '{$item->product->name}'.");
            }
        }

        DB::transaction(function () use ($user, $cartItems, $request) {
            // Calcular totales
            $subtotal = $cartItems->sum(
                fn($item) => $item->product->price * $item->quantity
            );

            // Crear la orden
            $order = Order::create([
                'user_id'  => $user->id,
                'subtotal' => $subtotal,
                'total'    => $subtotal,
                'status'   => Order::STATUS_PENDING,
                'notes'    => $request->get('notes'),
            ]);

            // Crear los ítems con snapshot de precio y nombre
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product->id,
                    'product_name' => $item->product->name,
                    'product_slug' => $item->product->slug,
                    'unit_price'   => $item->product->price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->product->price * $item->quantity,
                ]);


                // Descontar stock
                $item->product->decrementStock($item->quantity);
            }

            // Vaciar carrito
            $user->cartItems()->delete();

            // Guardar ID de orden en sesión para redirigir a PayPal
            session(['pending_order_id' => $order->id]);
        });

        $orderId = session('pending_order_id');

        return redirect()->route('orders.show', $orderId)
            ->with('success', '¡Pedido creado! Completá el pago para confirmar.');
    }

    /**
     * Cancelar una orden pendiente.
     */
    public function cancel(Order $order): RedirectResponse
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if (! $order->isCancellable()) {
            return back()->with('error', 'Esta orden no puede cancelarse.');
        }

        DB::transaction(function () use ($order) {
            // Restaurar stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);
        });

        return redirect()->route('orders.index')
            ->with('success', 'Orden cancelada correctamente.');
    }
}
