@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center">
        <ul class="inline-flex items-center -space-x-px">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li aria-disabled="true" aria-label="&laquo;">
                    <span class="inline-flex items-center px-3 py-1 border border-gray-200 bg-white text-gray-400 rounded">&laquo;</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3 py-1 border border-gray-200 bg-white text-blue-600 hover:bg-blue-50 rounded" aria-label="&laquo;">&laquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li aria-disabled="true"><span class="inline-flex items-center px-3 py-1 border border-gray-200 bg-white text-gray-500">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li aria-current="page"><span class="inline-flex items-center px-3 py-1 border border-blue-600 bg-blue-600 text-white rounded">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" class="inline-flex items-center px-3 py-1 border border-gray-200 bg-white text-blue-600 hover:bg-blue-50 rounded">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3 py-1 border border-gray-200 bg-white text-blue-600 hover:bg-blue-50 rounded" aria-label="&raquo;">&raquo;</a>
                </li>
            @else
                <li aria-disabled="true" aria-label="&raquo;">
                    <span class="inline-flex items-center px-3 py-1 border border-gray-200 bg-white text-gray-400 rounded">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
