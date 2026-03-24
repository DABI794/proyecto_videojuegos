<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Ver carrito — reemplaza carrito/ver_carrito.php
     */
    public function index(): View
    {
        $cartItems = auth()->user()
            ->cartItems()
            ->with('product.category')
            ->get();

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Agregar producto al carrito — reemplaza carrito/agregar_ajax.php
     * Responde JSON para peticiones AJAX o redirige para peticiones normales.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1|max:99',
        ]);

        $product  = Product::findOrFail($request->product_id);
        $quantity = $request->get('quantity', 1);

        // Verificar stock
        if (! $product->isInStock()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'exito'   => false,
                    'mensaje' => 'Producto sin stock disponible.',
                ], 422);
            }
            return back()->with('error', 'Producto sin stock disponible.');
        }

        // Si ya existe en carrito, incrementar cantidad
        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            CartItem::create([
                'user_id'    => auth()->id(),
                'product_id' => $product->id,
                'quantity'   => $quantity,
            ]);
        }

        $cartCount = auth()->user()->cartCount();

        if ($request->expectsJson()) {
            return response()->json([
                'exito'    => true,
                'mensaje'  => "'{$product->name}' agregado al carrito.",
                'cantidad' => $cartCount,
            ]);
        }

        return back()->with('success', "'{$product->name}' agregado al carrito.");
    }

    /**
     * Actualizar cantidad de un ítem del carrito.
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse|RedirectResponse
    {
        // Solo el dueño puede modificar su carrito
        abort_if($cartItem->user_id !== auth()->id(), 403);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cartItem->update(['quantity' => $request->quantity]);

        $total     = auth()->user()->cartTotal();
        $cartCount = auth()->user()->cartCount();

        if ($request->expectsJson()) {
            return response()->json([
                'exito'     => true,
                'subtotal'  => 'Bs. ' . number_format($cartItem->product->price * $request->quantity, 2),
                'total'     => 'Bs. ' . number_format($total, 2),
                'cantidad'  => $cartCount,
            ]);
        }

        return redirect()->route('cart.index');
    }

    /**
     * Eliminar un ítem del carrito.
     */
    public function destroy(CartItem $cartItem): JsonResponse|RedirectResponse
    {
        abort_if($cartItem->user_id !== auth()->id(), 403);

        $cartItem->delete();

        $cartCount = auth()->user()->cartCount();

        if (request()->expectsJson()) {
            return response()->json([
                'exito'    => true,
                'cantidad' => $cartCount,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Producto eliminado del carrito.');
    }

    /**
     * Vaciar carrito completo.
     */
    public function clear(): RedirectResponse
    {
        auth()->user()->cartItems()->delete();

        return redirect()->route('cart.index')->with('success', 'Carrito vaciado.');
    }

    /**
     * AJAX — cantidad total para el badge del navbar.
     * Reemplaza carrito/obtener_total.php del proyecto original.
     */
    public function total(): JsonResponse
    {
        $cantidad = auth()->check()
            ? auth()->user()->cartCount()
            : 0;

        return response()->json(['cantidad' => $cantidad]);
    }
}
