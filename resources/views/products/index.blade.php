@extends('layouts.app')
@section('title', 'Catálogo de Productos — GameStore')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#f1f5f9] mb-1">
            {{ $activeCategory ? $activeCategory->name : 'Todos los productos' }}
        </h1>
        <p class="text-[#64748b] text-sm">
            {{ $products->total() }} producto{{ $products->total() !== 1 ? 's' : '' }} encontrado{{ $products->total() !== 1 ? 's' : '' }}
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Sidebar filtros --}}
        <aside class="lg:w-64 shrink-0">
            <div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-5 sticky top-20">

                {{-- Búsqueda --}}
                <form action="{{ route('products.index') }}" method="GET" id="filter-form">
                    <h3 class="text-[#f1f5f9] font-semibold text-sm mb-3">Buscar</h3>
                    <input
                        type="text"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Nombre del juego..."
                        class="w-full bg-[#0f172a] border border-[#334155] text-[#f1f5f9] text-sm rounded-xl px-3 py-2 focus:outline-none focus:border-[#6366f1] placeholder-[#64748b] transition-colors mb-5"
                    >

                    {{-- Categorías --}}
                    <h3 class="text-[#f1f5f9] font-semibold text-sm mb-3">Categorías</h3>
                    <div class="space-y-1 mb-5">
                        <a href="{{ route('products.index', array_filter(['buscar' => request('buscar'), 'orden' => request('orden')])) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-sm no-underline transition-colors {{ ! request('categoria') ? 'bg-[#6366f1]/15 text-[#6366f1] font-medium' : 'text-[#94a3b8] hover:bg-[#334155] hover:text-[#f1f5f9]' }}">
                            <span>Todos</span>
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('products.index', array_filter(['categoria' => $category->slug, 'buscar' => request('buscar'), 'orden' => request('orden')])) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-sm no-underline transition-colors {{ request('categoria') === $category->slug ? 'bg-[#6366f1]/15 text-[#6366f1] font-medium' : 'text-[#94a3b8] hover:bg-[#334155] hover:text-[#f1f5f9]' }}">
                                <span>{{ $category->name }}</span>
                                <span class="text-xs text-[#64748b]">{{ $category->products_count }}</span>
                            </a>
                        @endforeach
                    </div>

                    {{-- Ordenar --}}
                    <h3 class="text-[#f1f5f9] font-semibold text-sm mb-3">Ordenar por</h3>
                    <select
                        name="orden"
                        onchange="this.form.submit()"
                        class="w-full bg-[#0f172a] border border-[#334155] text-[#94a3b8] text-sm rounded-xl px-3 py-2 focus:outline-none focus:border-[#6366f1] transition-colors"
                    >
                        @if(request('categoria')) <input type="hidden" name="categoria" value="{{ request('categoria') }}"> @endif
                        <option value="recientes" {{ request('orden', 'recientes') === 'recientes' ? 'selected' : '' }}>Más recientes</option>
                        <option value="precio_asc" {{ request('orden') === 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                        <option value="precio_desc" {{ request('orden') === 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                        <option value="nombre" {{ request('orden') === 'nombre' ? 'selected' : '' }}>Nombre A-Z</option>
                    </select>
                </form>
            </div>
        </aside>

        {{-- Grid productos --}}
        <div class="flex-1">
            @if($products->isEmpty())
                <div class="text-center py-20">
                    <div class="text-6xl mb-4">🎮</div>
                    <h3 class="text-[#f1f5f9] font-semibold text-xl mb-2">No se encontraron productos</h3>
                    <p class="text-[#64748b] mb-6">Probá con otro término de búsqueda o categoría.</p>
                    <a href="{{ route('products.index') }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white px-6 py-2.5 rounded-xl no-underline transition-colors text-sm font-medium">
                        Ver todos los productos
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                {{-- Paginación --}}
                @if($products->hasPages())
                    <div class="mt-10 flex justify-center">
                        {{ $products->links('partials.pagination') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ product_id: productoId, quantity: 1 }),
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
