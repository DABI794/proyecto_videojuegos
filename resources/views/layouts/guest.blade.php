<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GameStore Bolivia')</title>

    {{-- Inter Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Vite: Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- Cambiamos el bg-[#0f172a] por bg-[#1e293b] para que sea menos oscuro --}}
<body class="bg-[#1e293b] text-[#f1f5f9] font-sans min-h-screen flex flex-col justify-center items-center py-10">

    {{-- Logo Personalizado --}}
    <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 mb-8 no-underline hover:opacity-80 transition-all transform hover:scale-105">
        <span class="text-5xl">🎮</span>
        <div class="text-center">
            <span class="block text-[#f1f5f9] font-black text-2xl tracking-tighter">GAMESTORE</span>
            <span class="block text-[#6366f1] text-xs font-bold tracking-[0.2em] uppercase">Bolivia - Cochabamba</span>
        </div>
    </a>

    {{-- Contenido del formulario --}}
    <div class="w-full max-w-md px-4">
        {{-- Aquí se inyecta el formulario de Login/Register --}}
        <div class="bg-[#334155]/50 backdrop-blur-sm border border-[#475569] p-8 rounded-2xl shadow-2xl">
            {{ $slot }}
        </div>
    </div>