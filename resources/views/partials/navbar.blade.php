<nav class="fixed top-0 left-0 right-0 z-40 bg-[#0f172a]/95 backdrop-blur border-b border-[#334155]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}"
                class="flex items-center gap-2 text-white font-bold text-lg no-underline hover:opacity-80 transition-opacity">
                <span class="text-2xl">🎮</span>
                <span class="text-[#f1f5f9] font-semibold">GameStore</span>
            </a>

            {{-- Nav links (desktop) --}}
            <div class="hidden md:flex items-center gap-6">
                {{-- Enlaces móviles con estado activo para mantener consistencia con la navegación desktop --}}
                <a href="{{ route('home') }}"
                    class="block px-3 py-2 text-sm no-underline transition-colors {{ request()->routeIs('home') ? 'text-[#6366f1] font-medium' : 'text-[#94a3b8] hover:text-[#f1f5f9]' }}">
                    Inicio
                </a>

                <a href="{{ route('products.index') }}"
                    class="block px-3 py-2 text-sm no-underline transition-colors {{ request()->routeIs('products.*') ? 'text-[#6366f1] font-medium' : 'text-[#94a3b8] hover:text-[#f1f5f9]' }}">
                    Productos
                </a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-sm no-underline transition-colors {{ request()->routeIs('admin.*') ? 'text-[#6366f1] font-medium' : 'text-[#94a3b8] hover:text-[#f1f5f9]' }}">
                            Admin
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-3">

                {{-- Búsqueda rápida --}}
                <form action="{{ route('products.index') }}" method="GET" class="hidden sm:flex">
                    <div class="relative">
                        <input type="text" name="buscar" placeholder="Buscar juegos..." value="{{ request('buscar') }}"
                            class="bg-[#1e293b] border border-[#334155] text-[#f1f5f9] text-sm rounded-xl px-4 py-2 pr-9 w-48 focus:outline-none focus:border-[#6366f1] placeholder-[#64748b] transition-colors">
                        <button type="submit"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-[#64748b] hover:text-[#6366f1] transition-colors bg-transparent border-0">
                            <i class="bi bi-search text-sm"></i>
                        </button>
                    </div>
                </form>

                @auth
                    {{-- Carrito --}}
                    <a href="{{ route('cart.index') }}"
                        class="relative p-2 text-[#94a3b8] hover:text-[#f1f5f9] transition-colors no-underline">
                        <i class="bi bi-bag text-xl"></i>
                        @php $cartCount = auth()->user()->cartCount(); @endphp
                        <span id="carrito-badge"
                            class="absolute -top-0.5 -right-0.5 bg-[#6366f1] text-white text-xs font-bold rounded-full w-5 h-5 inline-flex items-center justify-center"
                            style="{{ $cartCount > 0 ? '' : 'display:none' }}">{{ $cartCount }}</span>
                    </a>

                    {{-- Usuario --}}
                    {{-- Dropdown de usuario controlado con Alpine.js para evitar JS inline y mejorar mantenibilidad --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center gap-2 bg-[#1e293b] border border-[#334155] rounded-xl px-3 py-2 text-sm text-[#94a3b8] hover:text-[#f1f5f9] hover:border-[#475569] transition-all">
                            <i class="bi bi-person-circle"></i>
                            <span class="hidden sm:inline max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down text-xs"></i>
                        </button>



                        {{-- Menú desplegable: se muestra con x-show y se cierra al hacer clic fuera --}}
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-[#1e293b] border border-[#334155] rounded-xl shadow-xl overflow-hidden z-50">
                            <a href="{{ route('orders.index') }}"
                                class="flex items-center gap-2 px-4 py-3 text-sm text-[#94a3b8] hover:bg-[#334155] hover:text-[#f1f5f9] no-underline transition-colors">
                                <i class="bi bi-receipt"></i> Mis pedidos
                            </a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-2 px-4 py-3 text-sm text-[#6366f1] hover:bg-[#334155] no-underline transition-colors">
                                    <i class="bi bi-speedometer2"></i> Panel admin
                                </a>
                            @endif
                            <div class="border-t border-[#334155]"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-400 hover:bg-[#334155] transition-colors bg-transparent border-0 text-left">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm text-[#94a3b8] hover:text-[#f1f5f9] no-underline transition-colors">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}"
                        class="text-sm bg-[#6366f1] hover:bg-[#4f46e5] text-white px-4 py-2 rounded-xl no-underline transition-colors font-medium">
                        Registrarse
                    </a>
                @endauth

                {{-- Menú mobile --}}
                <button class="md:hidden p-2 text-[#94a3b8] hover:text-[#f1f5f9] bg-transparent border-0"
                    onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <i class="bi bi-list text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-[#334155] py-3 space-y-1">
            <a href="{{ route('home') }}"
                class="block px-3 py-2 text-sm text-[#94a3b8] hover:text-[#f1f5f9] no-underline">Inicio</a>
            <a href="{{ route('products.index') }}"
                class="block px-3 py-2 text-sm text-[#94a3b8] hover:text-[#f1f5f9] no-underline">Productos</a>
            <form action="{{ route('products.index') }}" method="GET" class="px-3 py-2">
                <input type="text" name="buscar" placeholder="Buscar juegos..."
                    class="w-full bg-[#1e293b] border border-[#334155] text-[#f1f5f9] text-sm rounded-xl px-3 py-2 focus:outline-none focus:border-[#6366f1]">
            </form>
        </div>
    </div>
</nav>