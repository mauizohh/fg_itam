@if ($paginator->total() > 0)
    <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
        {{-- Showing Meta Text --}}
        <div class="text-muted font-medium">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </div>

        {{-- Pagination Buttons --}}
        <div class="flex items-center gap-1.5">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <span class="w-7 h-7 inline-flex items-center justify-center rounded-md border border-gray-100 bg-white text-gray-300 font-bold text-xs cursor-not-allowed select-none" aria-disabled="true" aria-label="Previous page">
                    &lt;
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="w-7 h-7 inline-flex items-center justify-center rounded-md border border-gray-200 bg-white text-ink hover:bg-gray-50 font-bold text-xs transition cursor-pointer select-none"
                   aria-label="Previous page">
                    &lt;
                </a>
            @endif

            {{-- Numeric Page Buttons / Sliding Window --}}
            @if ($paginator->lastPage() > 1)
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="w-7 h-7 inline-flex items-center justify-center text-xs font-semibold text-muted select-none">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="w-7 h-7 inline-flex items-center justify-center rounded-md bg-primary-600 text-white font-bold text-xs shadow-sm select-none" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="w-7 h-7 inline-flex items-center justify-center rounded-md border border-gray-200 bg-white text-ink hover:bg-gray-50 font-bold text-xs transition cursor-pointer select-none"
                                   aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @else
                <span class="w-7 h-7 inline-flex items-center justify-center rounded-md bg-primary-600 text-white font-bold text-xs shadow-sm select-none" aria-current="page">
                    1
                </span>
            @endif

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="w-7 h-7 inline-flex items-center justify-center rounded-md border border-gray-200 bg-white text-ink hover:bg-gray-50 font-bold text-xs transition cursor-pointer select-none"
                   aria-label="Next page">
                    &gt;
                </a>
            @else
                <span class="w-7 h-7 inline-flex items-center justify-center rounded-md border border-gray-100 bg-white text-gray-300 font-bold text-xs cursor-not-allowed select-none" aria-disabled="true" aria-label="Next page">
                    &gt;
                </span>
            @endif
        </div>
    </div>
@endif
