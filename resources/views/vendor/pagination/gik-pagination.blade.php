@if ($paginator->hasPages())
    <nav class="gik-pagination-container" aria-label="Pagination">
        <div class="gik-pagination-info">
            Menampilkan <span class="gik-font-bold">{{ $paginator->firstItem() }}</span>
            sampai <span class="gik-font-bold">{{ $paginator->lastItem() }}</span>
            dari <span class="gik-font-bold">{{ $paginator->total() }}</span> data
        </div>

        <ul class="gik-pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="gik-page-item disabled">
                    <span class="gik-page-link" aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li class="gik-page-item">
                    <a class="gik-page-link text-danger" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">&laquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="gik-page-item disabled"><span class="gik-page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="gik-page-item active" aria-current="page">
                                <span class="gik-page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="gik-page-item">
                                <a class="gik-page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="gik-page-item">
                    <a class="gik-page-link text-danger" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">&raquo;</a>
                </li>
            @else
                <li class="gik-page-item disabled">
                    <span class="gik-page-link" aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
        
        {{-- Mobile view --}}
        <div class="gik-pagination-mobile">
            @if ($paginator->onFirstPage())
                <span class="gik-btn disabled">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="gik-btn active">Sebelumnya</a>
            @endif

            <span class="gik-page-indicator">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="gik-btn active">Selanjutnya</a>
            @else
                <span class="gik-btn disabled">Selanjutnya</span>
            @endif
        </div>
    </nav>

    <style>
        .gik-pagination-container {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            margin: 1.5rem 0;
            gap: 1rem;
            font-family: inherit;
        }

        @media (min-width: 640px) {
            .gik-pagination-container {
                flex-direction: row;
            }
        }

        .gik-pagination-info {
            display: none;
            color: #6c757d;
            font-size: 0.875rem;
        }

        @media (min-width: 640px) {
            .gik-pagination-info {
                display: block;
            }
        }

        .gik-font-bold {
            font-weight: 600;
            color: #212529;
        }

        .gik-pagination-list {
            display: none;
            list-style: none;
            padding: 0;
            margin: 0;
            align-items: center;
        }

        @media (min-width: 640px) {
            .gik-pagination-list {
                display: flex;
            }
        }

        .gik-page-item {
            margin: 0 0.15rem;
        }

        .gik-page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 0.5rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            color: #6c757d;
            background-color: #fff;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .gik-page-link.text-danger {
            color: #dc3545;
        }

        .gik-page-link:hover {
            background-color: #fff1f2;
            color: #dc3545;
            border-color: #ffc2c8;
            transform: translateY(-1px);
        }

        .gik-page-item.active .gik-page-link {
            z-index: 3;
            color: #fff !important;
            background-color: #dc3545;
            border-color: #dc3545;
            box-shadow: 0 4px 6px -1px rgba(220, 53, 69, 0.4), 0 2px 4px -1px rgba(220, 53, 69, 0.2);
            transform: translateY(-1px);
        }

        .gik-page-item.disabled .gik-page-link {
            color: #adb5bd;
            pointer-events: none;
            background-color: #f8f9fa;
            border-color: #e9ecef;
        }

        .gik-pagination-mobile {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            gap: 0.5rem;
        }

        @media (min-width: 640px) {
            .gik-pagination-mobile {
                display: none;
            }
        }

        .gik-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.8125rem;
            font-weight: 600;
            text-transform: uppercase;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease-in-out;
        }

        .gik-btn.active {
            background-color: #fff;
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .gik-btn.active:hover {
            background-color: #dc3545;
            color: #fff;
        }

        .gik-btn.disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            pointer-events: none;
        }

        .gik-page-indicator {
            font-size: 0.8125rem;
            font-weight: 700;
            color: #495057;
            background-color: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            white-space: nowrap;
        }
    </style>
@endif
