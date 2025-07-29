@if ($paginator->hasPages())
    <div class="d-flex gap-2 text-muted small">
        <span>Pages:</span>
        
        {{-- First Page Link --}}
        @if ($paginator->currentPage() > 2 && $paginator->lastPage() > 3)
            <a href="{{ $paginator->url(1) }}" class="text-decoration-none">First</a>
            |
        @endif
        
        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span>{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <strong>{{ $page }}</strong>
                    @else
                        <a href="{{ $url }}" class="text-decoration-none">{{ $page }}</a>
                    @endif
                    
                    @if (!$loop->last)
                        |
                    @endif
                @endforeach
            @endif
        @endforeach
        
        {{-- Last Page Link --}}
        @if ($paginator->currentPage() < $paginator->lastPage() - 1 && $paginator->lastPage() > 3)
            |
            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="text-decoration-none">Last</a>
        @endif
    </div>
@endif