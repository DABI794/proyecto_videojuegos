@extends('layouts.app')
@section('title', 'Mis Pedidos — GameStore')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-3xl font-bold text-[#f1f5f9] mb-8">Mis Pedidos</h1>

    @if($orders->isEmpty())
        <div class="text-center py-20 bg-[#1e293b] border border-[#334155] rounded-2xl">
            <div class="text-6xl mb-4">📦</div>
            <h3 class="text-[#f1f5f9] font-semibold text-xl mb-2">Aún no tenés pedidos</h3>
            <p class="text-[#64748b] mb-6">Explorá el catálogo y hacé tu primera compra.</p>
            <a href="{{ route('products.index') }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white px-6 py-2.5 rounded-xl no-underline transition-colors font-medium">
                Ver productos
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-[#1e293b] border border-[#334155] hover:border-[#475569] rounded-2xl p-5 transition-colors">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-[#6366f1]/10 rounded-xl flex items-center justify-center">
                                <i class="bi bi-receipt text-[#6366f1]"></i>
                            </div>
                            <div>
                                <p class="text-[#f1f5f9] font-semibold text-sm">Pedido #{{ $order->id }}</p>
                                <p class="text-[#64748b] text-xs">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->items->count() }} producto{{ $order->items->count() !== 1 ? 's' : '' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium px-3 py-1 rounded-full {{ $order->status_color }}">
                                {{ $order->status_label }}
                            </span>
                            <span class="text-[#6366f1] font-bold">{{ $order->formatted_total }}</span>
                            <a href="{{ route('orders.show', $order) }}" class="bg-[#334155] hover:bg-[#475569] text-[#94a3b8] hover:text-[#f1f5f9] px-3 py-1.5 rounded-xl text-xs no-underline transition-colors">
                                Ver detalle
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links('partials.pagination') }}
        </div>
    @endif
</div>
@endsection
