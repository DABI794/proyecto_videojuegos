@extends('layouts.app')
@section('title', 'Gestión de Productos — Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#f1f5f9]">Productos</h1>
            <p class="text-[#64748b] text-sm mt-1">{{ $products->total() }} productos en total</p>
        </div>
        <a href="{{ route('admin.productos.create') }}" class="flex items-center gap-2 bg-[#6366f1] hover:bg-[#4f46e5] text-white px-4 py-2.5 rounded-xl no-underline transition-all font-medium text-sm hover:-translate-y-0.5">
            <i class="bi bi-plus-lg"></i> Nuevo producto
        </a>
    </div>

    <div class="bg-[#1e293b] border border-[#334155] rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-[#334155]">
                    <th class="text-left px-6 py-4 text-xs font-semibold text-[#64748b] uppercase tracking-wide">Producto</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-[#64748b] uppercase tracking-wide hidden sm:table-cell">Categoría</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-[#64748b] uppercase tracking-wide">Precio</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-[#64748b] uppercase tracking-wide hidden md:table-cell">Stock</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-[#64748b] uppercase tracking-wide hidden lg:table-cell">Estado</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#334155]">
                @foreach($products as $product)
                    <tr class="hover:bg-[#334155]/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-10 h-10 rounded-xl object-cover bg-[#0f172a] shrink-0">
                                <div class="min-w-0">
                                    <p class="text-[#f1f5f9] font-medium text-sm truncate max-w-[180px]">{{ $product->name }}</p>
                                    @if($product->is_featured)
                                        <span class="text-xs text-amber-400 bg-amber-400/10 px-1.5 py-0.5 rounded-full">Destacado</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <span class="text-[#94a3b8] text-sm">{{ $product->category?->name ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[#6366f1] font-bold text-sm">{{ $product->formatted_price }}</span>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="text-sm {{ $product->stock === 0 ? 'text-red-400' : ($product->stock <= 5 ? 'text-amber-400' : 'text-emerald-400') }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="text-xs px-2 py-1 rounded-full {{ $product->is_active ? 'text-emerald-400 bg-emerald-400/10' : 'text-red-400 bg-red-400/10' }}">
                                {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 justify-end">
                                <a href="{{ route('admin.productos.edit', $product) }}"
                                   class="p-2 text-[#64748b] hover:text-[#6366f1] hover:bg-[#6366f1]/10 rounded-lg transition-colors no-underline">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" 
                                        class="p-2 text-[#64748b] hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors bg-transparent border-0"
                                        onclick="mostrarModalEliminar('{{ route('admin.productos.destroy', $product) }}', '{{ addslashes($product->name) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-[#334155]">
                {{ $products->links('partials.pagination') }}
            </div>
        @endif
    </div>

    {{-- Modal de confirmación para eliminar --}}
    <div class="modal fade" id="modalEliminarProducto" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-[#1e293b] border-2 border-[#334155] shadow-2xl rounded-2xl">
                <div class="modal-header border-b border-[#334155] px-6 py-4">
                    <h5 class="modal-title text-[#f1f5f9] font-bold text-lg flex items-center gap-2" id="modalEliminarLabel">
                        <i class="bi bi-exclamation-triangle-fill text-red-400"></i>
                        Confirmar eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white opacity-50 hover:opacity-100" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body px-6 py-5">
                    <p class="text-[#94a3b8] mb-4">
                        ¿Estás seguro de que querés eliminar este producto?
                    </p>
                    <div class="bg-[#0f172a] border border-[#334155] rounded-xl p-4">
                        <p class="text-[#f1f5f9] font-semibold text-sm mb-1" id="nombreProductoEliminar">Nombre del producto</p>
                        <p class="text-[#64748b] text-xs">Esta acción no se puede deshacer. El producto será marcado como eliminado (soft delete).</p>
                    </div>
                </div>
                <div class="modal-footer border-t border-[#334155] px-6 py-4 gap-2">
                    <button type="button" class="px-4 py-2.5 bg-[#334155] hover:bg-[#475569] text-[#f1f5f9] rounded-xl transition-colors font-medium text-sm border-0" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </button>
                    <form id="formEliminarProducto" method="POST" class="m-0">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl transition-colors font-medium text-sm border-0">
                            <i class="bi bi-trash me-1"></i> Eliminar producto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let modalEliminar;

    document.addEventListener('DOMContentLoaded', function() {
        modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminarProducto'));
    });
    function mostrarModalEliminar(deleteUrl, productName) {
        // Actualizar el nombre del producto en el modal
        document.getElementById('nombreProductoEliminar').textContent = productName;
        
        // Actualizar la acción del formulario con la ruta correcta
        const form = document.getElementById('formEliminarProducto');
        form.action = deleteUrl;
        
        // Mostrar el modal
        modalEliminar.show();
    }
</script>
@endpush
@endsection
