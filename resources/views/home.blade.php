@extends('layouts.app')
@section('title', 'bienbenida por carlos — Videojuegos y Accesorios ')

@section('content')

{{-- Hero --}}
<section class="relative overflow-hidden bg-[#0f172a] pt-10 pb-20">
    <div class="absolute inset-0 bg-gradient-to-br from-[#6366f1]/10 via-transparent to-transparent pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="inline-block text-[#6366f1] text-sm font-medium bg-[#6366f1]/10 border border-[#6366f1]/20 px-4 py-1.5 rounded-full mb-6">
                🎮 La mejor tienda gaming de Bolivia
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-[#f1f5f9] leading-tight mb-6">
                Juegos, Consolas<br>
                <span class="text-[#6366f1]">y Accesorios</span>
            </h1>
            <p class="text-[#94a3b8] text-lg mb-8 leading-relaxed">
                Encontrá los últimos lanzamientos y los clásicos que nunca pasan de moda. Envíos a todo Bolivia.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('products.index') }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold px-8 py-3.5 rounded-xl no-underline transition-all hover:-translate-y-0.5">
                    Ver catálogo
                </a>
                @guest
                    <a href="{{ route('register') }}" class="bg-[#1e293b] hover:bg-[#334155] border border-[#334155] text-[#f1f5f9] font-semibold px-8 py-3.5 rounded-xl no-underline transition-all hover:-translate-y-0.5">
                        Crear cuenta
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>

{{-- Categorías --}}
@if($categories->isNotEmpty())
<section class="py-14 bg-[#0f172a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-[#f1f5f9] mb-8">Explorar por categoría</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['categoria' => $category->slug]) }}"
                   class="group bg-[#1e293b] border border-[#334155] hover:border-[#6366f1] rounded-xl p-4 no-underline transition-all hover:-translate-y-1">
                    <div class="text-[#6366f1] text-2xl mb-2">
                        @switch($category->slug)
                            @case('accion') 🗡️ @break
                            @case('aventura') 🗺️ @break
                            @case('rpg') ⚔️ @break
                            @case('deportes') ⚽ @break
                            @case('estrategia') ♟️ @break
                            @case('terror') 👻 @break
                            @case('simulacion') 🚀 @break
                            @case('shooter') 🔫 @break
                            @default 🎮
                        @endswitch
                    </div>
                    <div class="text-[#f1f5f9] font-semibold text-sm group-hover:text-[#6366f1] transition-colors">{{ $category->name }}</div>
                    <div class="text-[#64748b] text-xs mt-0.5">{{ $category->products_count }} juegos</div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Productos destacados --}}
@if($featuredProducts->isNotEmpty())
<section class="py-14 bg-[#0f172a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-[#f1f5f9]">Productos destacados</h2>
            <a href="{{ route('products.index') }}" class="text-[#6366f1] hover:text-[#818cf8] text-sm no-underline transition-colors">
                Ver todos <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featuredProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Últimos lanzamientos --}}
@if($latestProducts->isNotEmpty())
<section class="py-14 bg-[#0a1020]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-[#f1f5f9]">Últimos lanzamientos</h2>
            <a href="{{ route('products.index', ['orden' => 'recientes']) }}" class="text-[#6366f1] hover:text-[#818cf8] text-sm no-underline transition-colors">
                Ver todos <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($latestProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

@push('scripts')
<script>
async function agregarAlCarrito(productoId, btn) {
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Agregando...';

    try {
        const res = await fetch('{{ route("cart.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ product_id: productoId, quantity: 1 }),
        });
        const data = await res.json();
        if (data.exito) {
            btn.innerHTML = '<i class="bi bi-check-lg"></i> ¡Agregado!';
            btn.classList.replace('bg-[#6366f1]', 'bg-emerald-600');
            actualizarContadorCarrito();
            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.replace('bg-emerald-600', 'bg-[#6366f1]');
                btn.disabled = false;
            }, 2000);
        } else {
            btn.innerHTML = original;
            btn.disabled = false;
            alert(data.mensaje);
        }
    } catch (e) {
        btn.innerHTML = original;
        btn.disabled = false;
    }
}
</script>
@endpush
