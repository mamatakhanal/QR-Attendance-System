<body>
    <!-- Pagination -->
    @if ($paginator->hasPages())
    @php
    $current = $paginator->currentPage();
    $last = $paginator->lastPage();
    @endphp

    <div class="d-flex justify-content-between align-items-center mt-3">

        <div>
            Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }}
            of {{ $paginator->total() }} results
        </div>

        <nav>
            <ul class="pagination mb-0">

                {{-- Previous --}}
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}">&lsaquo;</a>
                </li>

                {{-- Pages --}}
                @for ($i = 1; $i <= $last; $i++)
                    <li class="page-item {{ $current == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($i) }}">
                        {{ $i }}
                    </a>
                    </li>
                    @endfor

                    {{-- Next --}}
                    <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}">&rsaquo;</a>
                    </li>

            </ul>
        </nav>

    </div>
    @endif
</body>