@extends('layouts.app')
@section('title', 'Pedido #' . $order->id . ' — GameStore')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#f1f5f9]">Pedido #{{ $order->id }}</h1>
            <p class="text-[#64748b] text-sm mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <span class="text-sm font-medium px-3 py-1.5 rounded-full {{ $order->status_color }}">
            {{ $order->status_label }}
        </span>
    </div>

    {{-- Success notice --}}
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Productos --}}
    <div class="bg-[#1e293b] border border-[#334155] rounded-2xl overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-[#334155]">
            <h2 class="text-[#f1f5f9] font-semibold">Productos</h2>
        </div>
        <div class="divide-y divide-[#334155]">
            @foreach($order->items as $item)
                <div class="flex items-center gap-4 px-6 py-4">
                    <div class="w-12 h-12 bg-[#0f172a] rounded-xl overflow-hidden shrink-0">
                        @if($item->product)
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[#64748b]"><i class="bi bi-controller"></i></div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#f1f5f9] font-medium text-sm truncate">{{ $item->product_name }}</p>
                        <p class="text-[#64748b] text-xs">{{ $item->formatted_unit_price }} × {{ $item->quantity }}</p>
                    </div>
                    <p class="text-[#f1f5f9] font-bold text-sm shrink-0">{{ $item->formatted_subtotal }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Totales --}}
    <div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-6 mb-6">
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-[#94a3b8]">Subtotal</span>
                <span class="text-[#f1f5f9]">Bs. {{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-[#94a3b8]">Envío</span>
                <span class="text-emerald-400">Gratis</span>
            </div>
            <div class="border-t border-[#334155] pt-3 flex justify-between">
                <span class="text-[#f1f5f9] font-bold">Total</span>
                <span class="text-[#6366f1] font-extrabold text-xl">{{ $order->formatted_total }}</span>
            </div>
        </div>
    </div>

    {{-- PayPal (solo si está pendiente) --}}
    @if($order->status === 'pending')
        <div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-6 mb-6">
            <h2 class="text-[#f1f5f9] font-semibold mb-4">Completar pago</h2>
            <p class="text-[#94a3b8] text-sm mb-4">Hacé clic en el botón de PayPal para completar tu compra de forma segura.</p>
            <div id="paypal-button-container"></div>
            <div id="payment-loading" class="hidden text-center py-4">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#6366f1]"></div>
                <p class="text-[#94a3b8] text-sm mt-2">Procesando pago...</p>
            </div>
        </div>
    @endif

    {{-- Acciones --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('orders.index') }}" class="flex items-center gap-2 bg-[#1e293b] border border-[#334155] text-[#94a3b8] hover:text-[#f1f5f9] hover:border-[#475569] px-4 py-2.5 rounded-xl no-underline transition-all text-sm">
            <i class="bi bi-arrow-left"></i> Mis pedidos
        </a>
        @if($order->isCancellable())
            <form action="{{ route('orders.cancel', $order) }}" method="POST"
                  onsubmit="return confirm('¿Cancelar este pedido?')">
                @csrf @method('PATCH')
                <button type="submit" class="flex items-center gap-2 bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 px-4 py-2.5 rounded-xl transition-all text-sm border-0" style="border: 1px solid rgba(239,68,68,0.3)">
                    <i class="bi bi-x-circle"></i> Cancelar pedido
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Modales --}}
<x-notification-modal id="success-modal" type="success" />
<x-notification-modal id="error-modal" type="error" />

@endsection

@if($order->status === 'pending')
@push('scripts')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=USD"></script>
<script>
const paypalButtonContainer = document.getElementById('paypal-button-container');
const paymentLoading = document.getElementById('payment-loading');

paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: { 
                    currency_code: 'USD', 
                    value: '{{ number_format($order->total / 6.96, 2, ".", "") }}' 
                },
                description: 'Pedido #{{ $order->id }} — GameStore Bolivia'
            }]
        });
    },
    
    onApprove: function(data, actions) {
        // Mostrar loading
        paypalButtonContainer.classList.add('hidden');
        paymentLoading.classList.remove('hidden');
        
        return actions.order.capture().then(function(details) {
            // Llamar al backend para confirmar el pago
            return fetch('{{ route("paypal.success", $order) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    paypal_order_id: data.orderID,
                    transaction_id: details.purchase_units[0].payments.captures[0].id
                })
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return res.json();
            })
            .then(data => {
                paymentLoading.classList.add('hidden');
                
                if (data.exito) {
                    // Mostrar modal de éxito
                    openNotificationModal(
                        'success-modal',
                        '¡Pago Exitoso! 🎉',
                        data.mensaje || '¡Tu pago ha sido confirmado exitosamente! Tu pedido está siendo procesado.',
                        () => {
                            // Recargar la página después de cerrar el modal
                            window.location.reload();
                        }
                    );
                } else {
                    // Mostrar modal de error
                    openNotificationModal(
                        'error-modal',
                        'Error en el Pago',
                        data.mensaje || 'Hubo un problema al procesar tu pago. Por favor, contactá con soporte.'
                    );
                    paypalButtonContainer.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                paymentLoading.classList.add('hidden');
                paypalButtonContainer.classList.remove('hidden');
                
                openNotificationModal(
                    'error-modal',
                    'Error de Conexión',
                    'No se pudo conectar con el servidor. Verificá tu conexión e intentá nuevamente.'
                );
            });
        });
    },
    
    onError: function(err) {
        console.error('PayPal Error:', err);
        
        openNotificationModal(
            'error-modal',
            'Error en PayPal',
            'Hubo un error al procesar el pago con PayPal. Por favor, intentá nuevamente o usá otro método de pago.'
        );
    },
    
    onCancel: function(data) {
        console.log('PayPal payment cancelled', data);
        
        openNotificationModal(
            'error-modal',
            'Pago Cancelado',
            'Cancelaste el pago. Podés intentar nuevamente cuando estés listo.'
        );
    }
}).render('#paypal-button-container');
</script>
@endpush
@endif
