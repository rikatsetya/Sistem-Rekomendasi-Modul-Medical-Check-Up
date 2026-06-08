@php
    $current = $paginator->currentPage();
    $last = $paginator->lastPage();
    $start = max(1, $current - 2);
    $end = min($last, $current + 2);
@endphp

{{-- FIRST PAGES --}}
@if ($start > 1)
    <a href="{{ $paginator->url(1) }}" class="paginate_button">1</a>

    @if ($start > 2)
        <a href="{{ $paginator->url(2) }}" class="paginate_button">2</a>
    @endif

    @if ($start > 3)
        <span class="paginate_button disabled">…</span>
    @endif
@endif

{{-- MIDDLE --}}
@for ($page = $start; $page <= $end; $page++)
    <a href="{{ $paginator->url($page) }}" class="paginate_button {{ $current == $page ? 'current' : '' }}">
        {{ $page }}
    </a>
@endfor

{{-- LAST --}}
@if ($end < $last)
    @if ($end < $last - 2)
        <span class="paginate_button disabled">…</span>
    @endif

    @if ($end < $last - 1)
        <a href="{{ $paginator->url($last - 1) }}" class="paginate_button">
            {{ $last - 1 }}
        </a>
    @endif

    <a href="{{ $paginator->url($last) }}" class="paginate_button">
        {{ $last }}
    </a>
@endif
