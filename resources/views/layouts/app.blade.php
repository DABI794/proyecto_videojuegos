<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tienda de Videojuegos')</title>

    {{-- Inter Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Vite: Tailwind CSS + JS --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[#0f172a] text-[#f1f5f9] font-sans min-h-screen">

    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="fixed top-20 right-4 z-50 max-w-sm">
            <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl shadow-lg" id="flash-success">
                <i class="bi bi-check-circle-fill"></i>
                <span class="text-sm">{{ session('success') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-emerald-400/60 hover:text-emerald-400">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-20 right-4 z-50 max-w-sm">
            <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl shadow-lg" id="flash-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span class="text-sm">{{ session('error') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-400/60 hover:text-red-400">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Contenido principal --}}
    <main class="pt-16">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Cart badge updater global --}}
    <script>
        async function actualizarContadorCarrito() {
            try {
                const res = await fetch('{{ route("cart.total") }}');
                const data = await res.json();
                const badge = document.querySelector('#carrito-badge');
                if (badge) {
                    badge.textContent = data.cantidad;
                    badge.style.display = data.cantidad > 0 ? 'inline-flex' : 'none';
                }
            } catch (e) {}
        }

        // Auto-ocultar flash messages
        setTimeout(() => {
            ['flash-success','flash-error'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.closest('.fixed')?.remove();
            });
        }, 4000);
    </script>

    @stack('scripts')
</body>
</html>
