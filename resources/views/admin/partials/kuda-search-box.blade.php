@php
    $queryWithoutSearch = request()->except('search');
    $resetSearchUrl = $action . (count($queryWithoutSearch) > 0 ? '?' . http_build_query($queryWithoutSearch) : '');
@endphp

<form action="{{ $action }}" method="GET" class="kuda-search-form">
    @if(request()->filled('gender'))
        <input type="hidden" name="gender" value="{{ request('gender') }}">
    @endif

    @if(request()->filled('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif

    <div class="kuda-search-panel">
        <span class="material-symbols-rounded kuda-search-icon">search</span>

        <input
            type="text"
            name="search"
            class="form-control kuda-search-input"
            placeholder="{{ $placeholder ?? 'Cari nama kuda, jenis, atau peternakan' }}"
            value="{{ request('search') }}"
        >

        @if(request()->filled('search'))
            <a href="{{ $resetSearchUrl }}" class="kuda-search-clear" title="Reset search">
                &times;
            </a>
        @endif

        <button class="btn bg-gradient-dark mb-0 kuda-search-button" type="submit">
            Search
        </button>
    </div>
</form>
