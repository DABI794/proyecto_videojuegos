@if ($paginator->hasPages())
    <nav class="flex items-center justify-center gap-1">

        {{-- Anterior --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-[#64748b] bg-[#1e293b] border border-[#334155] rounded-xl text-sm cursor-not-allowed">
                <i class="bi bi-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-[#94a3b8] hover:text-[#f1f5f9] bg-[#1e293b] border border-[#334155] hover:border-[#475569] rounded-xl text-sm no-underline transition-colors">
                <i class="bi bi-chevron-left"></i>
            </a>
        @endif

        {{-- Páginas --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-2 text-[#64748b] text-sm">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-2 bg-[#6366f1] text-white rounded-xl text-sm font-medium">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-2 text-[#94a3b8] hover:text-[#f1f5f9] bg-[#1e293b] border border-[#334155] hover:border-[#475569] rounded-xl text-sm no-underline transition-colors">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Siguiente --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-[#94a3b8] hover:text-[#f1f5f9] bg-[#1e293b] border border-[#334155] hover:border-[#475569] rounded-xl text-sm no-underline transition-colors">
                <i class="bi bi-chevron-right"></i>
            </a>
        @else
            <span class="px-3 py-2 text-[#64748b] bg-[#1e293b] border border-[#334155] rounded-xl text-sm cursor-not-allowed">
                <i class="bi bi-chevron-right"></i>
            </span>
        @endif
    </nav>
@endif
