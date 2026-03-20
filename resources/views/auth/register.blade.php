<x-guest-layout>
    <div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-8 shadow-xl">

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-[#f1f5f9] mb-1">Crear cuenta</h1>
            <p class="text-[#64748b] text-sm">Únete a GameStore Bolivia</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- Nombre --}}
            <div>
                <label for="name" class="block text-sm font-medium text-[#94a3b8] mb-1.5">Nombre completo</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full bg-[#0f172a] border {{ $errors->has('name') ? 'border-red-500/50' : 'border-[#334155]' }} text-[#f1f5f9] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1]/30 placeholder-[#64748b] transition-colors"
                    placeholder="Tu nombre"
                >
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-[#94a3b8] mb-1.5">Correo electrónico</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                    class="w-full bg-[#0f172a] border {{ $errors->has('email') ? 'border-red-500/50' : 'border-[#334155]' }} text-[#f1f5f9] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1]/30 placeholder-[#64748b] transition-colors"
                    placeholder="tu@correo.com"
                >
                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-[#94a3b8] mb-1.5">Contraseña</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="w-full bg-[#0f172a] border {{ $errors->has('password') ? 'border-red-500/50' : 'border-[#334155]' }} text-[#f1f5f9] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1]/30 placeholder-[#64748b] transition-colors"
                    placeholder="Mínimo 8 caracteres"
                >
                @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-[#94a3b8] mb-1.5">Confirmar contraseña</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full bg-[#0f172a] border border-[#334155] text-[#f1f5f9] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1]/30 placeholder-[#64748b] transition-colors"
                    placeholder="Repetí la contraseña"
                >
                @error('password_confirmation') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold py-3 rounded-xl transition-all hover:-translate-y-0.5 border-0 text-sm"
            >
                Crear cuenta
            </button>
        </form>

        <p class="text-center text-sm text-[#64748b] mt-6">
            ¿Ya tenés cuenta?
            <a href="{{ route('login') }}" class="text-[#6366f1] hover:text-[#818cf8] no-underline font-medium transition-colors">
                Iniciá sesión
            </a>
        </p>
    </div>
</x-guest-layout>
