<div class="pagination" style="border-bottom: 1px solid #d6dadd;">
    @if ($paginator->onFirstPage())
        <span class="pagenav disabled">«</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagenav">«</a>
    @endif

    {{-- Номера страниц --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="pagenav disabled">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="pagenav current">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagenav">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagenav">»</a>
    @else
        <span class="pagenav disabled">»</span>
    @endif
</div>
