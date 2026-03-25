@extends('layouts.app')
@section('title', 'Mi Carrito — GameStore')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-3xl font-bold text-[#f1f5f9] mb-8">
        <i class="bi bi-bag me-2 text-[#6366f1]"></i> Mi Carrito
    </h1>

    @if($cartItems->isEmpty())
        <div class="flex flex-col items-center justify-center text-center py-24 bg-[#1e293b] border border-[#334155] rounded-3xl shadow-lg shadow-black/20">
            <div class="w-24 h-24 bg-[#0f172a] rounded-full flex items-center justify-center mb-6 border border-[#334155]">
                <i class="bi bi-cart-x text-4xl text-[#64748b]"></i>
            </div>
            <h3 class="text-[#f1f5f9] font-bold text-2xl mb-3">Tu carrito está vacío</h3>
            <p class="text-[#94a3b8] mb-8 max-w-sm">Aún no has agregado ningún producto. ¡Explora nuestro catálogo y encuentra tus juegos favoritos!</p>
            <a href="{{ route('products.index') }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white px-8 py-3 rounded-xl no-underline transition-all duration-300 hover:-translate-y-1 font-semibold flex items-center gap-2 shadow-lg shadow-[#6366f1]/20">
                <i class="bi bi-controller"></i> Ir a la tienda
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Items --}}
            <div class="lg:col-span-2 space-y-4" id="cart-items">
                @foreach($cartItems as $item)
                    <div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-4 flex flex-col sm:flex-row gap-4 sm:items-center transition-all duration-300 hover:border-[#475569] hover:bg-[#1e293b]/80 group" id="item-{{ $item->id }}">

                        {{-- Imagen --}}
                        <div class="relative shrink-0 w-24 h-24 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-[#0f172a] border border-[#334155] group-hover:border-[#6366f1]/50 transition-colors">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <span class="text-[11px] text-[#6366f1] font-bold uppercase tracking-wider mb-1">{{ $item->product->category?->name ?? 'Juego' }}</span>
                            <a href="{{ route('products.show', $item->product) }}" class="text-[#f1f5f9] font-semibold text-base leading-tight line-clamp-2 hover:text-[#6366f1] transition-colors decoration-transparent">{{ $item->product->name }}</a>
                            <p class="text-[#cbd5e1] font-bold mt-1.5">{{ $item->product->formatted_price }}</p>
                            <div class="flex items-center gap-2 mt-3">
                                <button onclick="actualizarCantidad({{ $item->id }}, -1, {{ $item->quantity }})"
                                    class="w-7 h-7 rounded-lg bg-[#0f172a] border border-[#334155] text-[#94a3b8] hover:text-white hover:border-[#6366f1] transition-all text-sm">−</button>
                                <span id="qty-{{ $item->id }}" class="text-[#f1f5f9] font-semibold w-6 text-center">{{ $item->quantity }}</span>
                                <button onclick="actualizarCantidad({{ $item->id }}, +1, {{ $item->quantity }})"
                                    class="w-7 h-7 rounded-lg bg-[#0f172a] border border-[#334155] text-[#94a3b8] hover:text-white hover:border-[#6366f1] transition-all text-sm">+</button>
                                <button onclick="eliminarItem({{ $item->id }})"
                                    class="ml-auto text-[#64748b] hover:text-red-400 transition-colors text-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Resumen --}}
            <div class="lg:col-span-1">
                <div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-6 sticky top-20">
                    <h3 class="text-[#f1f5f9] font-bold text-lg mb-5">Resumen</h3>

                    <div class="space-y-3 mb-5">
                        <div class="flex justify-between text-sm">
                            <span class="text-[#94a3b8]">Subtotal</span>
                            <span class="text-[#f1f5f9] font-medium" id="total-display">Bs. {{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#94a3b8]">Envío</span>
                            <span class="text-emerald-400 font-medium">Gratis</span>
                        </div>
                    </div>

                    <div class="border-t border-[#334155] pt-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-[#f1f5f9] font-bold">Total</span>
                            <span class="text-[#6366f1] font-extrabold text-xl" id="total-final">Bs. {{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <textarea name="notes" placeholder="Nota para tu pedido (opcional)..."
                            class="w-full bg-[#0f172a] border border-[#334155] text-[#94a3b8] text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#6366f1] placeholder-[#64748b] resize-none mb-4"
                            rows="2"></textarea>
                        <button type="submit"
                            class="w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold py-3 rounded-xl transition-all hover:-translate-y-0.5 border-0 flex items-center justify-center gap-2">
                            <i class="bi bi-credit-card"></i> Proceder al pago
                        </button>
                    </form>

                    <form action="{{ route('cart.clear') }}" method="POST" class="mt-3"
                          onsubmit="return confirm('¿Vaciar el carrito?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-transparent border border-[#334155] text-[#64748b] hover:text-red-400 hover:border-red-400/30 text-sm py-2.5 rounded-xl transition-all">
                            <i class="bi bi-trash me-1"></i> Vaciar carrito
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function actualizarCantidad(itemId, delta, actual) {
    const nueva = Math.max(1, actual + delta);
    if (nueva === actual) return;

    try {
        const res = await fetch(`/carrito/${itemId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-HTTP-Method-Override': 'PATCH' },
            body: JSON.stringify({ quantity: nueva, _method: 'PATCH' }),
        });
        const data = await res.json();
        if (data.exito) {
            document.getElementById(`qty-${itemId}`).textContent = nueva;
            document.getElementById(`subtotal-${itemId}`).textContent = data.subtotal;
            document.getElementById('total-display').textContent = data.total;
            document.getElementById('total-final').textContent = data.total;
            actualizarContadorCarrito();
            // Actualizar el delta para el próximo click
            document.querySelectorAll(`[onclick*="actualizarCantidad(${itemId}"]`).forEach(btn => {
                btn.setAttribute('onclick', btn.getAttribute('onclick').replace(/,\s*\d+\)$/, `, ${nueva})`));
            });
        }
    } catch(e) {}
}

async function eliminarItem(itemId) {
    try {
        const res = await fetch(`/carrito/${itemId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ _method: 'DELETE' }),
        });
        const data = await res.json();
        if (data.exito) {
            document.getElementById(`item-${itemId}`)?.remove();
            actualizarContadorCarrito();
            if (data.cantidad === 0) location.reload();
        }
    } catch(e) {}
}
</script>
@endpush
