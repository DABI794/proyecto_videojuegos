<footer class="bg-[#0f172a] border-t border-[#334155] mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- 🔹 BLOQUE SUPERIOR (CONTACTO / INFO RÁPIDA) -->
        <div class="mb-10 text-center">
            <h3 class="text-[#f1f5f9] text-lg font-semibold mb-2">
                ¿Necesitas ayuda?
            </h3>
            <p class="text-[#64748b] text-sm mb-4">
                Contáctanos para soporte, pedidos o consultas sobre productos.
            </p>

            <div class="flex justify-center gap-4 flex-wrap">
                <span class="text-[#64748b] text-sm">📞 +591 79325139</span>
                <span class="text-[#64748b] text-sm">✉️ soporte@eabmodel.com</span>
                <span class="text-[#64748b] text-sm">📍 Bolivia</span>
            </div>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center md:text-left">

            {{-- Brand --}}
            <div>
                <div class="flex items-center justify-center gap-3 mb-4 group cursor-pointer">
                    <span class="text-3xl transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6">
                        🎮
                    </span>
                    <span class="text-[#f1f5f9] font-bold text-xl tracking-wide transition-colors duration-300 group-hover:text-[#6366f1]">
                        GameStore
                    </span>
                </div>

                <p class="text-[#64748b] text-sm leading-relaxed max-w-xs mx-auto">
                    Tu tienda de videojuegos y accesorios gaming en Bolivia. Los mejores títulos al mejor precio.
                </p>

                <!-- Redes -->
                <div class="flex justify-center gap-4 mt-4">
                    <a href="#" class="text-[#64748b] hover:text-[#6366f1] transition-transform hover:scale-110">🌐</a>
                    <a href="#" class="text-[#64748b] hover:text-[#6366f1] transition-transform hover:scale-110">📘</a>
                    <a href="#" class="text-[#64748b] hover:text-[#6366f1] transition-transform hover:scale-110">📸</a>
                    <a href="#" class="text-[#64748b] hover:text-[#6366f1] transition-transform hover:scale-110">🎵</a>
                </div>
            </div>

            {{-- Links --}}
            <div>
                <h6 class="text-[#f1f5f9] font-semibold text-sm mb-5 uppercase tracking-wider">
                    Tienda
                </h6>

                <ul class="space-y-3 flex flex-col items-center md:items-start w-full">
                    <li class="w-full">
                        <a href="{{ route('products.index') }}" 
                           class="block w-full text-center md:text-left text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-all duration-300 hover:translate-x-1 relative after:content-[''] after:block after:w-0 after:h-[2px] after:bg-[#6366f1] after:transition-all after:duration-300 hover:after:w-full">
                            🎮 Todos los productos
                        </a>
                    </li>

                    @foreach(\App\Models\Category::active()->take(4)->get() as $cat)
                        <li class="w-full">
                            <a href="{{ route('products.index', ['categoria' => $cat->slug]) }}" 
                               class="block w-full text-center md:text-left text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-all duration-300 hover:translate-x-1 relative after:content-[''] after:block after:w-0 after:h-[2px] after:bg-[#6366f1] after:transition-all after:duration-300 hover:after:w-full">
                                {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Cuenta --}}
            <div>
                <h6 class="text-[#f1f5f9] font-semibold text-sm mb-5 uppercase tracking-wider">
                    Mi cuenta
                </h6>

                <ul class="space-y-3 flex flex-col items-center md:items-start w-full">

                    @auth
                        <li class="w-full">
                            <a href="{{ route('orders.index') }}" 
                               class="block w-full text-center md:text-left text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-all duration-300 hover:translate-x-1 relative after:content-[''] after:block after:w-0 after:h-[2px] after:bg-[#6366f1] after:transition-all after:duration-300 hover:after:w-full">
                                📦 Mis pedidos
                            </a>
                        </li>

                        <li class="w-full">
                            <a href="{{ route('cart.index') }}" 
                               class="block w-full text-center md:text-left text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-all duration-300 hover:translate-x-1 relative after:content-[''] after:block after:w-0 after:h-[2px] after:bg-[#6366f1] after:transition-all after:duration-300 hover:after:w-full">
                                🛒 Mi carrito
                            </a>
                        </li>
                    @else
                        <li class="w-full">
                            <a href="{{ route('login') }}" 
                               class="block w-full text-center md:text-left text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-all duration-300 hover:translate-x-1 relative after:content-[''] after:block after:w-0 after:h-[2px] after:bg-[#6366f1] after:transition-all after:duration-300 hover:after:w-full">
                                🔑 Iniciar sesión
                            </a>
                        </li>

                        <li class="w-full">
                            <a href="{{ route('register') }}" 
                               class="block w-full text-center md:text-left text-[#64748b] hover:text-[#6366f1] text-sm no-underline transition-all duration-300 hover:translate-x-1 relative after:content-[''] after:block after:w-0 after:h-[2px] after:bg-[#6366f1] after:transition-all after:duration-300 hover:after:w-full">
                                📝 Registrarse
                            </a>
                        </li>
                    @endauth

                </ul>
            </div>
        </div>

        <!-- Bottom -->
        <div class="border-t border-[#334155] mt-12 pt-6 flex justify-center items-center text-center">
            <p class="text-[#64748b] text-xs tracking-wide hover:text-[#6366f1] transition-colors duration-300">
                © {{ date('Y') }} EABMODEL. Todos los derechos reservados.
            </p>
        </div>

    </div>
</footer>