@extends('layouts.admin')

@section('title', 'Resumen de Ventas')

@section('content')
<div class="mb-10">
    <h1 class="text-3xl font-extrabold text-[#f1f5f9] font-outfit mb-2">Dashboard</h1>
    <p class="text-[#94a3b8]">Vista general del rendimiento de tu tienda.</p>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-[#1e293b] border border-[#334155] p-6 rounded-3xl shadow-sm">
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-emerald-500/10 p-3 rounded-2xl text-emerald-500">
                <i class="bi bi-currency-dollar text-xl"></i>
            </div>
            <span class="text-[#94a3b8] font-medium">Ventas Totales</span>
        </div>
        <div class="text-3xl font-bold text-[#f1f5f9] tracking-tight">Bs. {{ number_format($stats['total_sales'], 2) }}</div>
    </div>

    <div class="bg-[#1e293b] border border-[#334155] p-6 rounded-3xl shadow-sm">
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-[#6366f1]/10 p-3 rounded-2xl text-[#6366f1]">
                <i class="bi bi-bag-check text-xl"></i>
            </div>
            <span class="text-[#94a3b8] font-medium">Pedidos</span>
        </div>
        <div class="text-3xl font-bold text-[#f1f5f9] tracking-tight">{{ $stats['orders_count'] }}</div>
    </div>

    <div class="bg-[#1e293b] border border-[#334155] p-6 rounded-3xl shadow-sm">
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-amber-500/10 p-3 rounded-2xl text-amber-500">
                <i class="bi bi-people text-xl"></i>
            </div>
            <span class="text-[#94a3b8] font-medium">Clientes</span>
        </div>
        <div class="text-3xl font-bold text-[#f1f5f9] tracking-tight">{{ $stats['users_count'] }}</div>
    </div>

    <div class="bg-[#1e293b] border border-[#334155] p-6 rounded-3xl shadow-sm">
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-red-500/10 p-3 rounded-2xl text-red-500">
                <i class="bi bi-exclamation-triangle text-xl"></i>
            </div>
            <span class="text-[#94a3b8] font-medium">Sin Stock</span>
        </div>
        <div class="text-3xl font-bold text-[#f1f5f9] tracking-tight">{{ $stats['out_of_stock'] }}</div>
    </div>
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Sales Chart --}}
    <div class="bg-[#1e293b] border border-[#334155] p-8 rounded-3xl shadow-sm">
        <h3 class="text-xl font-bold text-[#f1f5f9] mb-8">Rendimiento de Ventas (Últimos 7 días)</h3>
        <canvas id="salesChart" height="200"></canvas>
    </div>

    {{-- Category Distribution --}}
    <div class="bg-[#1e293b] border border-[#334155] p-8 rounded-3xl shadow-sm">
        <h3 class="text-xl font-bold text-[#f1f5f9] mb-8">Distribución por Categorías</h3>
        <div class="max-w-[300px] mx-auto">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Sales Chart
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesData->pluck('date')) !!},
            datasets: [{
                label: 'Ventas en BS',
                data: {!! json_encode($salesData->pluck('total')) !!},
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#6366f1',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(51, 65, 85, 0.5)' }, ticks: { color: '#94a3b8' } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Category Chart
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryData->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($categoryData->pluck('total')) !!},
                backgroundColor: [
                    '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#94a3b8', padding: 20, font: { size: 12 } } }
            },
            cutout: '70%'
        }
    });
</script>
@endpush
