@php
    $sortValue = request()->filled('sort') ? request('sort') : 'terbaru';
    $isFilterActive = request()->filled('gender') || ($sortValue !== 'terbaru');
@endphp

<form action="{{ $action }}" method="GET" class="m-0">
    @if(request()->filled('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
    @endif

    <div class="dropdown kuda-filter-dropdown">
        <button
            class="btn btn-sm kuda-filter-btn {{ $isFilterActive ? 'bg-gradient-dark text-white' : 'btn-outline-dark' }} mb-0"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">
            <span class="material-symbols-rounded kuda-filter-icon">filter_alt</span>
            <span>Filter</span>
            <span class="kuda-filter-caret"></span>
        </button>

        <div class="dropdown-menu {{ $menuAlignment ?? '' }} kuda-filter-menu p-3 shadow">
            <p class="text-xs text-uppercase text-secondary font-weight-bold mb-3">
                Filter Data Kuda
            </p>

            <div class="mb-3">
                <label class="form-label text-xs mb-1">Gender</label>
                <select name="gender" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">Semua Gender</option>
                    <option value="jantan" {{ request('gender') === 'jantan' ? 'selected' : '' }}>Jantan</option>
                    <option value="betina" {{ request('gender') === 'betina' ? 'selected' : '' }}>Betina</option>
                </select>
            </div>

            <div>
                <label class="form-label text-xs mb-1">Urutkan</label>
                <select name="sort" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="terbaru" {{ $sortValue === 'terbaru' ? 'selected' : '' }}>Terbaru ditambahkan</option>
                    <option value="terlama" {{ $sortValue === 'terlama' ? 'selected' : '' }}>Terlama ditambahkan</option>
                    <option value="nama_asc" {{ $sortValue === 'nama_asc' ? 'selected' : '' }}>Nama Kuda A-Z</option>
                    <option value="nama_desc" {{ $sortValue === 'nama_desc' ? 'selected' : '' }}>Nama Kuda Z-A</option>
                </select>
            </div>
        </div>
    </div>
</form>
