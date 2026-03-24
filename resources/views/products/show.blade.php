@extends('layouts.app')
@section('title', $product->name . ' — GameStore')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-[#64748b] mb-8">
        <a href="{{ route('home') }}" class="hover:text-[#6366f1] no-underline transition-colors">Inicio</a>
        <i class="bi bi-chevron-right text-xs"></i>
        <a href="{{ route('products.index') }}" class="hover:text-[#6366f1] no-underline transition-colors">Productos</a>
        @if($product->category)
            <i class="bi bi-chevron-right text-xs"></i>
            <a href="{{ route('products.index', ['categoria' => $product->category->slug]) }}" class="hover:text-[#6366f1] no-underline transition-colors">{{ $product->category->name }}</a>
        @endif
        <i class="bi bi-chevron-right text-xs"></i>
        <span class="text-[#94a3b8] truncate max-w-[200px]">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- Imagen --}}
        <div class="bg-[#1e293b] border border-[#334155] rounded-2xl overflow-hidden aspect-square">
            <img
                src="{{ $product->image_url }}"
                alt="{{ $product->name }}"
                class="w-full h-full object-cover"
            >
        </div>

        {{-- Info --}}
        <div class="flex flex-col">
            @if($product->category)
                <span class="text-[#6366f1] text-sm font-medium uppercase tracking-wide mb-2">
                    {{ $product->category->name }}
                </span>
            @endif

            <h1 class="text-3xl font-bold text-[#f1f5f9] mb-4">{{ $product->name }}</h1>

            {{-- Calificación promedio --}}
            @if($product->reviews()->count() > 0)
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star-fill {{ $i <= $product->averageRating() ? '' : 'text-[#334155]' }}"></i>
                        @for($i = 1; $i <= 5; $i++)
                        @endfor
                    </div>
                    <span class="text-sm text-[#94a3b8]">({{ $product->reviews()->count() }} reseñas)</span>
                </div>
            @endif


            {{-- Precio --}}
            <div class="flex items-center gap-4 mb-6">
                <span class="text-4xl font-extrabold text-[#6366f1]">{{ $product->formatted_price }}</span>
                @if($product->isInStock())
                    <span class="text-sm text-emerald-400 bg-emerald-400/10 border border-emerald-400/20 px-3 py-1 rounded-full">
                        <i class="bi bi-check-circle me-1"></i> En stock ({{ $product->stock }} disponibles)
                    </span>
                @else
                    <span class="text-sm text-red-400 bg-red-400/10 border border-red-400/20 px-3 py-1 rounded-full">
                        <i class="bi bi-x-circle me-1"></i> Sin stock
                    </span>
                @endif
            </div>

            {{-- Descripción --}}
            @if($product->description)
                <p class="text-[#94a3b8] leading-relaxed mb-8">{{ $product->description }}</p>
            @endif

            {{-- Agregar al carrito --}}
            @if($product->isInStock())
                @auth
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center bg-[#1e293b] border border-[#334155] rounded-xl overflow-hidden">
                            <button onclick="cambiarCantidad(-1)" class="px-4 py-3 text-[#94a3b8] hover:text-[#f1f5f9] hover:bg-[#334155] transition-colors bg-transparent border-0 font-bold">−</button>
                            <input type="number" id="cantidad" value="1" min="1" max="{{ $product->stock }}"
                                class="w-14 text-center bg-transparent text-[#f1f5f9] text-sm border-0 focus:outline-none py-3">
                            <button onclick="cambiarCantidad(1)" class="px-4 py-3 text-[#94a3b8] hover:text-[#f1f5f9] hover:bg-[#334155] transition-colors bg-transparent border-0 font-bold">+</button>
                        </div>
                        <button
                            onclick="agregarAlCarrito({{ $product->id }}, this)"
                            class="flex-1 bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold py-3 rounded-xl transition-all hover:-translate-y-0.5 border-0 flex items-center justify-center gap-2"
                        >
                            <i class="bi bi-bag-plus"></i> Agregar al carrito
                        </button>
                    </div>
                    <a href="{{ route('cart.index') }}" class="w-full text-center bg-[#1e293b] hover:bg-[#334155] border border-[#334155] text-[#94a3b8] hover:text-[#f1f5f9] font-medium py-3 rounded-xl no-underline transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-bag"></i> Ver carrito
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold py-3 rounded-xl no-underline transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-person"></i> Iniciá sesión para comprar
                    </a>
                @endauth
            @else
                <button disabled class="w-full bg-[#334155] text-[#64748b] font-semibold py-3 rounded-xl cursor-not-allowed border-0">
                    Sin stock disponible
                </button>
            @endif
        </div>
    </div>

    {{-- Productos relacionados --}}
    @if($relatedProducts->isNotEmpty())
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-[#f1f5f9] mb-6">Productos relacionados</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($relatedProducts as $related)
                    @include('partials.product-card', ['product' => $related])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Reseñas --}}
    <div class="mt-16 border-t border-[#334155] pt-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            {{-- Formulario de reseña --}}
            <div class="lg:col-span-1">
                <h3 class="text-xl font-bold text-[#f1f5f9] mb-6">Deja tu opinión</h3>
                
                @auth
                    <form action="{{ route('reviews.store', $product) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm text-[#94a3b8] mb-2">Calificación</label>
                            <select name="rating" class="w-full bg-[#1e293b] border border-[#334155] text-[#f1f5f9] rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#6366f1] focus:outline-none">
                                <option value="5">⭐⭐⭐⭐⭐ (Excelente)</option>
                                <option value="4">⭐⭐⭐⭐ (Muy bueno)</option>
                                <option value="3">⭐⭐⭐ (Bueno)</option>
                                <option value="2">⭐⭐ (Regular)</option>
                                <option value="1">⭐ (Malo)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-[#94a3b8] mb-2">Tu comentario</label>
                            <textarea name="comment" rows="4" class="w-full bg-[#1e293b] border border-[#334155] text-[#f1f5f9] rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#6366f1] focus:outline-none placeholder:text-[#475569]" placeholder="Cuéntanos qué te pareció el juego..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold py-3 rounded-xl transition-all">
                            Enviar reseña
                        </button>
                    </form>
                @else
                    <div class="bg-[#1e293b]/50 border border-[#334155] rounded-2xl p-6 text-center">
                        <p class="text-[#94a3b8] mb-4">Debes iniciar sesión para dejar una reseña.</p>
                        <a href="{{ route('login') }}" class="inline-block text-[#6366f1] font-semibold hover:underline">Iniciar sesión</a>
                    </div>
                @endauth
            </div>

            {{-- Lista de reseñas --}}
            <div class="lg:col-span-2">
                <h3 class="text-xl font-bold text-[#f1f5f9] mb-6">Reseñas de la comunidad</h3>
                
                @if($product->reviews()->count() > 0)
                    <div class="space-y-6">
                        @foreach($product->reviews()->with('user')->latest()->get() as $review)
                            <div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-[#334155] flex items-center justify-center text-[#6366f1] font-bold">
                                            {{ substr($review->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-[#f1f5f9] font-semibold text-sm">{{ $review->user->name }}</p>
                                            <p class="text-[#64748b] text-xs">{{ $review->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex text-yellow-400 text-xs">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star-fill {{ $i <= $review->rating ? '' : 'text-[#334155]' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="text-[#94a3b8] text-sm leading-relaxed">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-[#1e293b]/30 rounded-2xl border border-dashed border-[#334155]">
                        <i class="bi bi-chat-dots text-4xl text-[#334155] mb-4 block"></i>
                        <p class="text-[#64748b]">Aún no hay reseñas para este producto. ¡Sé el primero en opinar!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
function cambiarCantidad(delta) {
    const input = document.getElementById('cantidad');
    const max = parseInt(input.max);
    const nuevo = Math.min(Math.max(parseInt(input.value) + delta, 1), max);
    input.value = nuevo;
}

async function agregarAlCarrito(productoId, btn) {
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Agregando...';
    try {
        const res = await fetch('{{ route("cart.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ product_id: productoId, quantity: cantidad }),
        });
        const data = await res.json();
        if (data.exito) {
            btn.innerHTML = '<i class="bi bi-check-lg"></i> ¡Agregado!';
            btn.classList.replace('bg-[#6366f1]','bg-emerald-600');
            actualizarContadorCarrito();
            setTimeout(() => { btn.innerHTML = original; btn.classList.replace('bg-emerald-600','bg-[#6366f1]'); btn.disabled = false; }, 2000);
        } else { btn.innerHTML = original; btn.disabled = false; alert(data.mensaje); }
    } catch(e) { btn.innerHTML = original; btn.disabled = false; }
}
</script>
@endpush
