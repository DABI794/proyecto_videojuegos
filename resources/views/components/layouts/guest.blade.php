<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GameStore') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f172a] text-[#f1f5f9] font-sans min-h-screen flex flex-col justify-center items-center py-10">

    <a href="{{ route('home') }}" class="flex items-center gap-2 mb-8 no-underline hover:opacity-80 transition-opacity">
        <span class="text-3xl">🎮</span>
        <span class="text-[#f1f5f9] font-bold text-xl">GameStore</span>
    </a>

    <div class="w-full max-w-md px-4">
        {{ $slot }}
    </div>

    <p class="text-[#64748b] text-xs mt-8">© {{ date('Y') }} GameStore Bolivia</p>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
