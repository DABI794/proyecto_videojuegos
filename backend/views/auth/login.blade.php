<x-guest-layout>
    <div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-8 shadow-xl">

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-[#f1f5f9] mb-1">Iniciar sesión</h1>
            <p class="text-[#64748b] text-sm">Bienvenido de vuelta a GameStore</p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm px-4 py-3 rounded-xl">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-[#94a3b8] mb-1.5">
                    Correo electrónico
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full bg-[#0f172a] border {{ $errors->has('email') ? 'border-red-500/50' : 'border-[#334155]' }} text-[#f1f5f9] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1]/30 placeholder-[#64748b] transition-colors"
                    placeholder="tu@correo.com"
                >
                @error('email')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-[#94a3b8]">
                        Contraseña
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-[#6366f1] hover:text-[#818cf8] no-underline transition-colors">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full bg-[#0f172a] border {{ $errors->has('password') ? 'border-red-500/50' : 'border-[#334155]' }} text-[#f1f5f9] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1]/30 placeholder-[#64748b] transition-colors"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center gap-2">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 accent-[#6366f1]"
                >
                <label for="remember_me" class="text-sm text-[#64748b] cursor-pointer">
                    Recordarme
                </label>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold py-3 rounded-xl transition-all hover:-translate-y-0.5 border-0 text-sm"
            >
                Iniciar sesión
            </button>
        </form>

        {{-- Register link --}}
        <p class="text-center text-sm text-[#64748b] mt-6">
            ¿No tenés cuenta?
            <a href="{{ route('register') }}" class="text-[#6366f1] hover:text-[#818cf8] no-underline font-medium transition-colors">
                Registrate gratis
            </a>
        </p>
    </div>
</x-guest-layout>
