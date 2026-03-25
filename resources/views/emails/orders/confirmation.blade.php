<x-mail::message>
# ¡Gracias por tu compra!

Hola {{ $order->user->name }}, tu pedido **#{{ $order->id }}** ha sido registrado correctamente.

<x-mail::table>
| Producto | Cantidad | Subtotal |
| :--- | :---: | :--- |
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | Bs. {{ number_format($order->total, 2) }} |
@endforeach
| **Total** | | **Bs. {{ number_format($order->total, 2) }}** |
</x-mail::table>

<x-mail::button :url="route('orders.show', $order)">
Ver mi pedido
</x-mail::button>

Gracias,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>

