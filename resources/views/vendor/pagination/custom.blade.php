@if ($paginator->hasPages())
    @php
        $itemName = $itemName ?? 'items';
        if (isset($itemName) && is_string($itemName)) {
            try {
                $translated = __('messages.' . $itemName);
                // If translation doesn't exist, Laravel returns the key with messages. prefix
                $itemName = (strpos($translated, 'messages.') === 0) ? $itemName : $translated;
            } catch (\Exception $e) {
                // If translation fails, use the original key
                $itemName = $itemName;
            }
        }
    @endphp
    <div class="custom-pagination">
        <div class="pagination-info">
            <span class="pagination-text">
                @if($paginator->total() > 0)
                    {{ __('messages.showing') }} {{ $paginator->firstItem() }} {{ __('messages.of') }} {{ $paginator->total() }} {{ $itemName }}
                @else
                    {{ __('messages.showing') }} 0 {{ __('messages.of') }} 0 {{ $itemName }}
                @endif
            </span>
        </div>
        <div class="pagination-controls">
            @if ($paginator->onFirstPage())
                <button class="pagination-btn disabled" disabled>
                    <i class="fas fa-chevron-right"></i>
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn" rel="prev">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-ellipsis">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button class="pagination-btn active" disabled>{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" rel="next">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @else
                <button class="pagination-btn disabled" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
            @endif
        </div>
    </div>
@endif

