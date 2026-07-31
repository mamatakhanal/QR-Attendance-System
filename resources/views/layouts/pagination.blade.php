<body>

    <!-- Pagination -->

    @if ($paginator->hasPages())



        @php

            $current = $paginator->currentPage();

            $last = $paginator->lastPage();

        @endphp



        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-3">



            <!-- Result Count -->

            <div class="text-muted small">

                Showing

                <strong>{{ $paginator->firstItem() ?? 0 }}</strong>

                to

                <strong>{{ $paginator->lastItem() ?? 0 }}</strong>

                of

                <strong>{{ $paginator->total() }}</strong>

                results

            </div>



            <!-- Pagination -->

            <nav aria-label="Pagination">

                <ul class="pagination pagination-sm mb-0">



                    {{-- Previous --}}

                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled">

                            <span class="page-link">&lsaquo;</span>

                        </li>
                    @else
                        <li class="page-item">

                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">

                                &lsaquo;

                            </a>

                        </li>
                    @endif





                    {{-- First Page --}}

                    @if ($current > 3)

                        <li class="page-item">

                            <a class="page-link" href="{{ $paginator->url(1) }}">

                                1

                            </a>

                        </li>



                        @if ($current > 4)
                            <li class="page-item disabled">

                                <span class="page-link">...</span>

                            </li>
                        @endif

                    @endif





                    {{-- Page Numbers --}}

                    @for ($i = max(1, $current - 2); $i <= min($last, $current + 2); $i++)
                        @if ($i == $current)
                            <li class="page-item active">

                                <span class="page-link">

                                    {{ $i }}

                                </span>

                            </li>
                        @else
                            <li class="page-item">

                                <a class="page-link" href="{{ $paginator->url($i) }}">

                                    {{ $i }}

                                </a>

                            </li>
                        @endif
                    @endfor





                    {{-- Last Page --}}

                    @if ($current < $last - 2)



                        @if ($current < $last - 3)
                            <li class="page-item disabled">

                                <span class="page-link">...</span>

                            </li>
                        @endif



                        <li class="page-item">

                            <a class="page-link" href="{{ $paginator->url($last) }}">

                                {{ $last }}

                            </a>

                        </li>



                    @endif





                    {{-- Next --}}

                    @if ($paginator->hasMorePages())
                        <li class="page-item">

                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">

                                &rsaquo;

                            </a>

                        </li>
                    @else
                        <li class="page-item disabled">

                            <span class="page-link">&rsaquo;</span>

                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif

</body>
