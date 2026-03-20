<footer class="bg-[#0f172a] border-t border-[#334155] mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl">🎮</span>
                    <span class="text-[#f1f5f9] font-bold text-lg">GameStore</span>
                </div>
                <p class="text-[#64748b] text-sm leading-relaxed">
                    Tu tienda de videojuegos y accesorios gaming en Bolivia. Los mejores títulos al mejor precio.
                </p>
            </div>

            {{-- Links --}}
            <div>
                <h6 class="text-[#f1f5f9] font-semibold text-sm mb-4">Tienda</h6>
                <ul class="space-y-2">
                    <li><a href="{{ route('products.index') }}" class="text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-colors">Todos los productos</a></li>
                    @foreach(\App\Models\Category::active()->take(4)->get() as $cat)
                        <li><a href="{{ route('products.index', ['categoria' => $cat->slug]) }}" class="text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-colors">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Cuenta --}}
            <div>
                <h6 class="text-[#f1f5f9] font-semibold text-sm mb-4">Mi cuenta</h6>
                <ul class="space-y-2">
                    @auth
                        <li><a href="{{ route('orders.index') }}" class="text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-colors">Mis pedidos</a></li>
                        <li><a href="{{ route('cart.index') }}" class="text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-colors">Mi carrito</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-colors">Iniciar sesión</a></li>
                        <li><a href="{{ route('register') }}" class="text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-colors">Registrarse</a></li>
                    @endauth
                </ul>
            </div>
        </div>

        <div class="border-t border-[#334155] mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
            <p class="text-[#64748b] text-xs">© {{ date('Y') }} GameStore Bolivia. Todos los derechos reservados.</p>
            <p class="text-[#64748b] text-xs">Hecho con Laravel 12 + Tailwind CSS</p>
        </div>
    </div>
</footer>
