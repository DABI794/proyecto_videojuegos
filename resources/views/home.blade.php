@extends('layouts.app')
@section('title', 'Bienvenida por Carlos — Videojuegos y Accesorios')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@section('content')

{{-- Hero --}}
{{-- Cambiamos bg-[#0f172a] por bg-surface-darkest (tu verde oliva) --}}
<section class="relative overflow-hidden bg-surface-darkest pt-10 pb-20">
    <div class="absolute inset-0 bg-gradient-to-br from-brand/10 via-transparent to-transparent pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            {{-- Cambiamos los bordes y texto al amarillo 'brand' --}}
            <span class="inline-block text-brand text-sm font-medium bg-brand/10 border border-brand/20 px-4 py-1.5 rounded-full mb-6">
                🎮 La mejor tienda gaming de Bolivia creada por carlitos
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-[#f1f5f9] leading-tight mb-6">
                Juegos, Consolas<br>
                {{-- Aquí aplicamos el color brand (amarillo) --}}
                <span class="text-brand">y Accesorios</span>
            </h1>
            <p class="text-slate-400 text-lg mb-8 leading-relaxed">
                Encontrá los últimos lanzamientos y los clásicos que nunca pasan de moda. Envíos a todo Bolivia.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                {{-- Botón principal con tus nuevos colores --}}
                <a href="{{ route('products.index') }}" class="bg-brand hover:bg-brand-hover text-black font-semibold px-8 py-3.5 rounded-xl no-underline transition-all hover:-translate-y-0.5">
                    Ver catálogo
                </a>
                @guest
                    <a href="{{ route('register') }}" class="bg-surface-dark hover:bg-surface-medium border border-surface-medium text-white font-semibold px-8 py-3.5 rounded-xl no-underline transition-all hover:-translate-y-0.5">
                        Crear cuenta
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>

{{-- Categorías --}}
@if($categories->isNotEmpty())
<section class="py-16 bg-[#0b1120]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Título --}}
        <h2 class="text-3xl font-extrabold text-center mb-12 
bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 
text-transparent bg-clip-text 
drop-shadow-[0_0_10px_rgba(139,92,246,0.6)]">
    Explorar por categoría
</h2>

        @php
            $colors = [
                'accion' => 'text-red-500',
                'aventura' => 'text-green-400',
                'rpg' => 'text-purple-400',
                'deportes' => 'text-yellow-400',
                'estrategia' => 'text-blue-400',
                'terror' => 'text-pink-500',
                'simulacion' => 'text-cyan-400',
                'shooter' => 'text-orange-500',
            ];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['categoria' => $category->slug]) }}"
                   class="group relative flex flex-col items-center justify-center text-center 
                   bg-[#111827]/80 backdrop-blur-md 
                   border border-[#1f2937] 
                   rounded-2xl p-6 
                   transition-all duration-300 
                   hover:-translate-y-2 hover:shadow-[0_0_25px_rgba(99,102,241,0.6)] 
                   hover:border-indigo-500">

                    {{-- Glow interno --}}
                    <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 
                    transition duration-300 bg-gradient-to-br from-indigo-500/10 to-purple-500/10"></div>

                    {{-- Icono --}}
<div class="relative text-4xl mb-3 transition-all duration-300 
group-hover:scale-125 group-hover:rotate-6 
group-hover:drop-shadow-[0_0_10px_rgba(99,102,241,0.8)]">

    @switch($category->slug)
        @case('accion') 
            <i class="fa-solid fa-gun text-red-500"></i> 
        @break

        @case('aventura') 
            <i class="fa-solid fa-map text-green-400"></i> 
        @break

        @case('rpg') 
            <i class="fa-solid fa-dragon text-purple-400"></i> 
        @break

        @case('deportes') 
            <i class="fa-solid fa-futbol text-yellow-400"></i> 
        @break

        @case('estrategia') 
            <i class="fa-solid fa-chess text-blue-400"></i> 
        @break

        @case('terror') 
            <i class="fa-solid fa-skull text-pink-500"></i> 
        @break

        @case('simulacion') 
            <i class="fa-solid fa-car text-cyan-400"></i> 
        @break

        @case('shooter') 
            <i class="fa-solid fa-crosshairs text-orange-500"></i> 
        @break

        @default 
            <i class="fa-solid fa-gamepad text-indigo-400"></i>
    @endswitch
                    </div>

                    {{-- Nombre --}}
                    <div class="relative text-[#e5e7eb] font-semibold text-sm 
                    group-hover:text-indigo-400 transition-colors">
                        {{ $category->name }}
                    </div>

                    {{-- Badge --}}
                    <div class="relative mt-3 px-3 py-1 text-xs 
                    bg-[#1f2937] rounded-full text-[#9ca3af] 
                    group-hover:bg-indigo-500 group-hover:text-white transition">
                        {{ $category->products_count }} juegos
                    </div>

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
            <h2 class="text-3xl font-extrabold text-center mb-12 
bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 
text-transparent bg-clip-text 
drop-shadow-[0_0_10px_rgba(139,92,246,0.6)]">
    Productos destacados
</h2>
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
            <h2 class="text-3xl font-extrabold text-center mb-12 
bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 
text-transparent bg-clip-text 
drop-shadow-[0_0_10px_rgba(139,92,246,0.6)]">
    Ultimos lanzamientos
</h2>
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
