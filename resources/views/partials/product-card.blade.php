{{-- Uso: @include('partials.product-card', ['product' => $product]) --}}
<div class="group bg-[#1e293b] border border-[#334155] rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:border-[#6366f1] hover:shadow-xl hover:shadow-[#6366f1]/10 flex flex-col">

    {{-- Imagen --}}
    <a href="{{ route('products.show', $product) }}" class="block overflow-hidden aspect-[4/3] bg-[#0f172a]">
        <img
            src="{{ $product->image_url }}"
            alt="{{ $product->name }}"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            loading="lazy"
        >
    </a>

    {{-- Contenido --}}
    <div class="p-4 flex flex-col flex-1">

        {{-- Categoría --}}
        @if($product->category)
            <span class="text-xs text-[#6366f1] font-medium uppercase tracking-wide mb-1">
                {{ $product->category->name }}
            </span>
        @endif

        {{-- Nombre --}}
        <h3 class="text-[#f1f5f9] font-semibold text-sm leading-snug mb-2 line-clamp-2 flex-1">
            <a href="{{ route('products.show', $product) }}" class="no-underline hover:text-[#6366f1] transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Precio + Stock --}}
        <div class="flex items-center justify-between mb-3">
            <span class="text-[#6366f1] font-bold text-lg">{{ $product->formatted_price }}</span>

            @if($product->isInStock())
                <span class="text-xs text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded-full">
                    En stock
                </span>
            @else
                <span class="text-xs text-red-400 bg-red-400/10 px-2 py-0.5 rounded-full">
                    Sin stock
                </span>
            @endif
        </div>

        {{-- Botón agregar al carrito --}}
        @if($product->isInStock())
            @auth
                <button
                    onclick="agregarAlCarrito({{ $product->id }}, this)"
                    class="w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white text-sm font-medium py-2.5 rounded-xl transition-all duration-200 hover:-translate-y-0.5 border-0 flex items-center justify-center gap-2"
                >
                    <i class="bi bi-bag-plus"></i>
                    Agregar al carrito
                </button>
            @else
                <a href="{{ route('login') }}" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white text-sm font-medium py-2.5 rounded-xl transition-all no-underline text-center flex items-center justify-center gap-2">
                    <i class="bi bi-bag-plus"></i>
                    Agregar al carrito
                </a>
            @endauth
        @else
            <button disabled class="w-full bg-[#334155] text-[#64748b] text-sm font-medium py-2.5 rounded-xl cursor-not-allowed border-0">
                Sin stock
            </button>
        @endif
    </div>
</div>
