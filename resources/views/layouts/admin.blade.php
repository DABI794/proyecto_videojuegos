<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard — @yield('title', config('app.name'))</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#0f172a] text-[#f1f5f9] min-h-screen">
    
    <div class="flex">
        {{-- Sidebar --}}
        <aside class="w-64 bg-[#1e293b] border-r border-[#334155] min-h-screen sticky top-0">
            <div class="p-6 border-b border-[#334155] mb-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline">
                    <div class="bg-[#6366f1] p-2 rounded-lg text-white">
                        <i class="bi bi-controller"></i>
                    </div>
                    <span class="text-xl font-bold font-outfit text-white tracking-tight">GameStore <span class="text-[#6366f1] text-sm">ADMIN</span></span>
                </a>
            </div>

            <nav class="px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#6366f1] text-white shadow-lg shadow-[#6366f1]/20' : 'text-[#94a3b8] hover:bg-[#334155] hover:text-[#f1f5f9]' }} no-underline font-medium">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.productos.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.productos.*') ? 'bg-[#6366f1] text-white' : 'text-[#94a3b8] hover:bg-[#334155] hover:text-[#f1f5f9]' }} no-underline font-medium">
                    <i class="bi bi-box-seam"></i> Productos
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#94a3b8] hover:bg-[#334155] hover:text-[#f1f5f9] no-underline font-medium">
                    <i class="bi bi-cart-check"></i> Pedidos
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#94a3b8] hover:bg-[#334155] hover:text-[#f1f5f9] no-underline font-medium">
                    <i class="bi bi-people"></i> Usuarios
                </a>
            </nav>

            <div class="absolute bottom-10 left-0 w-full px-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-500/10 no-underline font-medium border-0 bg-transparent cursor-pointer">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main --}}
        <main class="flex-1 p-10">
            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
