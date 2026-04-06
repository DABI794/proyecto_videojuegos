{{-- Modal de notificación simple sin Alpine.js --}}
@props([
    'id' => 'notification-modal',
    'type' => 'success' // success, error, warning, info
])

@php
$colors = [
    'success' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/30', 'text' => 'text-emerald-400', 'icon' => 'bi-check-circle-fill'],
    'error'   => ['bg' => 'bg-red-500/10', 'border' => 'border-red-500/30', 'text' => 'text-red-400', 'icon' => 'bi-x-circle-fill'],
    'warning' => ['bg' => 'bg-yellow-500/10', 'border' => 'border-yellow-500/30', 'text' => 'text-yellow-400', 'icon' => 'bi-exclamation-triangle-fill'],
    'info'    => ['bg' => 'bg-blue-500/10', 'border' => 'border-blue-500/30', 'text' => 'text-blue-400', 'icon' => 'bi-info-circle-fill'],
];
$color = $colors[$type] ?? $colors['info'];
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0">
    <div class="bg-[#1e293b] border border-[#334155] rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95" id="{{ $id }}-content">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-[#334155]">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full {{ $color['bg'] }} border {{ $color['border'] }} flex items-center justify-center">
                    <i class="bi {{ $color['icon'] }} {{ $color['text'] }} text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-[#f1f5f9]" id="{{ $id }}-title">Notificación</h3>
            </div>
            <button onclick="closeNotificationModal('{{ $id }}')" class="text-[#64748b] hover:text-[#f1f5f9] transition-colors bg-transparent border-0 p-1">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6">
            <p class="text-[#94a3b8] leading-relaxed" id="{{ $id }}-message">
                {{ $slot }}
            </p>
        </div>

        {{-- Footer --}}
        <div class="flex gap-3 p-6 pt-0">
            <button onclick="closeNotificationModal('{{ $id }}')" 
                class="flex-1 bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold py-3 rounded-xl transition-all border-0">
                Aceptar
            </button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function openNotificationModal(modalId, title, message, onClose = null) {
    const modal = document.getElementById(modalId);
    const content = document.getElementById(`${modalId}-content`);
    
    if (title) document.getElementById(`${modalId}-title`).textContent = title;
    if (message) document.getElementById(`${modalId}-message`).textContent = message;
    
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
    
    // Guardar callback de cierre si existe
    if (onClose) {
        modal.dataset.onClose = onClose.toString();
    }
}

function closeNotificationModal(modalId) {
    const modal = document.getElementById(modalId);
    const content = document.getElementById(`${modalId}-content`);
    
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.style.display = 'none';
        
        // Ejecutar callback si existe
        if (modal.dataset.onClose) {
            try {
                eval(modal.dataset.onClose)();
                delete modal.dataset.onClose;
            } catch(e) {}
        }
    }, 300);
}

// Cerrar modal al hacer clic en el fondo
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('fixed') && e.target.classList.contains('z-50')) {
        const modalId = e.target.id;
        if (modalId) closeNotificationModal(modalId);
    }
});

// Cerrar modal con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id$="-modal"]').forEach(modal => {
            if (modal.style.display !== 'none') {
                closeNotificationModal(modal.id);
            }
        });
    }
});
</script>
@endpush
@endonce
