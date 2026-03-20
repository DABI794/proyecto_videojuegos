@extends('layouts.app')
@section('title', 'Panel de Administración — GameStore')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-[#f1f5f9]">Panel de Administración</h1>
            <p class="text-[#64748b] text-sm mt-1">Bienvenido, {{ auth()->user()->name }}</p>
        </div>
        <a href="{{ route('admin.productos.create') }}" class="flex items-center gap-2 bg-[#6366f1] hover:bg-[#4f46e5] text-white px-4 py-2.5 rounded-xl no-underline transition-all font-medium text-sm hover:-translate-y-0.5">
            <i class="bi bi-plus-lg"></i> Nuevo producto
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        @php
        $statCards = [
            ['label' => 'Productos activos', 'value' => $stats['total_products'], 'icon' => 'bi-controller', 'color' => 'text-[#6366f1] bg-[#6366f1]/10'],
            ['label' => 'Clientes', 'value' => $stats['total_users'], 'icon' => 'bi-people', 'color' => 'text-blue-400 bg-blue-400/10'],
            ['label' => 'Pedidos totales', 'value' => $stats['total_orders'], 'icon' => 'bi-receipt', 'color' => 'text-amber-400 bg-amber-400/10'],
            ['label' => 'Ingresos (Bs.)', 'value' => number_format($stats['total_revenue'], 2), 'icon' => 'bi-cash-stack', 'color' => 'text-emerald-400 bg-emerald-400/10'],
        ];
        @endphp
        @foreach($statCards as $card)
            <div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[#64748b] text-xs font-medium uppercase tracking-wide">{{ $card['label'] }}</span>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ $card['color'] }}">
                        <i class="bi {{ $card['icon'] }} text-base"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-[#f1f5f9]">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Alertas --}}
    @if($stats['pending_orders'] > 0 || $stats['low_stock'] > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            @if($stats['pending_orders'] > 0)
                <div class="flex items-center gap-3 bg-amber-400/10 border border-amber-400/20 text-amber-400 px-4 py-3 rounded-xl text-sm">
                    <i class="bi bi-clock-history text-lg"></i>
                    <span>{{ $stats['pending_orders'] }} pedido{{ $stats['pending_orders'] !== 1 ? 's' : '' }} pendiente{{ $stats['pending_orders'] !== 1 ? 's' : '' }} de pago</span>
                </div>
            @endif
            @if($stats['low_stock'] > 0)
                <div class="flex items-center gap-3 bg-red-400/10 border border-red-400/20 text-red-400 px-4 py-3 rounded-xl text-sm">
                    <i class="bi bi-exclamation-triangle text-lg"></i>
                    <span>{{ $stats['low_stock'] }} producto{{ $stats['low_stock'] !== 1 ? 's' : '' }} con stock bajo</span>
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Pedidos recientes --}}
        <div class="bg-[#1e293b] border border-[#334155] rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-[#334155] flex items-center justify-between">
                <h2 class="text-[#f1f5f9] font-semibold">Pedidos recientes</h2>
            </div>
            <div class="divide-y divide-[#334155]">
                @forelse($recentOrders as $order)
                    <div class="px-6 py-3 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[#f1f5f9] text-sm font-medium">#{{ $order->id }} — {{ $order->user?->name }}</p>
                            <p class="text-[#64748b] text-xs">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $order->status_color }}">{{ $order->status_label }}</span>
                            <span class="text-[#6366f1] font-bold text-sm">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-[#64748b] text-sm">Sin pedidos aún.</div>
                @endforelse
            </div>
        </div>

        {{-- Stock bajo --}}
        <div class="bg-[#1e293b] border border-[#334155] rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-[#334155] flex items-center justify-between">
                <h2 class="text-[#f1f5f9] font-semibold">Stock bajo</h2>
                <a href="{{ route('admin.productos.index') }}" class="text-xs text-[#6366f1] hover:text-[#818cf8] no-underline">Ver todos</a>
            </div>
            <div class="divide-y divide-[#334155]">
                @forelse($lowStockProducts as $product)
                    <div class="px-6 py-3 flex items-center justify-between gap-4">
                        <p class="text-[#f1f5f9] text-sm font-medium truncate">{{ $product->name }}</p>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs {{ $product->stock === 0 ? 'text-red-400 bg-red-400/10' : 'text-amber-400 bg-amber-400/10' }} px-2 py-0.5 rounded-full font-medium">
                                {{ $product->stock }} uds.
                            </span>
                            <a href="{{ route('admin.productos.edit', $product) }}" class="text-xs text-[#6366f1] hover:text-[#818cf8] no-underline">Editar</a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-[#64748b] text-sm">✅ Todo el stock está bien.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
