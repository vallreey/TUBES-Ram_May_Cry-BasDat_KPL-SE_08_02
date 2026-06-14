@extends('layouts.material')

@section('title', 'Marketplace')
@section('breadcrumb', 'Marketplace')

@push('styles')
    <link rel="stylesheet" href="{{ asset('material/css/lib_sendiri/kuda-toolbar.css') }}">
@endpush

@section('content')

@php
    $hasActiveQuery = request()->filled('search')
        || request()->filled('gender')
        || (request()->filled('sort') && request('sort') !== 'terbaru');
@endphp

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-start flex-wrap kuda-card-header">
                    <div>
                        <h6 class="mb-1">Marketplace Kuda</h6>
                        <p class="text-sm mb-0">Daftar kuda yang tersedia dan sudah terjual</p>
                    </div>

                    @include('admin.partials.kuda-search-box', [
                        'action' => url()->current(),
                    ])
                </div>
            </div>

            <div class="px-4 pt-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 kuda-toolbar-row pt-3">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('marketplace.index', request()->only(['search', 'gender', 'sort'])) }}"
                           class="btn btn-sm {{ $page === 'tersedia' ? 'bg-gradient-success text-white' : 'btn-outline-success' }} mb-0">
                            Kuda Tersedia
                        </a>

                        <a href="{{ route('marketplace.terjual', request()->only(['search', 'gender', 'sort'])) }}"
                           class="btn btn-sm {{ $page === 'terjual' ? 'bg-gradient-danger text-white' : 'btn-outline-danger' }} mb-0">
                            Kuda Terjual
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                        @if($hasActiveQuery)
                            <div class="text-end me-1">
                                <p class="text-xs text-secondary mb-1">
                                    Menampilkan {{ $kuda->count() }} data berdasarkan search/filter.
                                </p>
                                <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary mb-0">
                                    Reset Semua
                                </a>
                            </div>
                        @endif

                        @include('admin.partials.kuda-filter-dropdown', [
                            'action' => url()->current(),
                            'menuAlignment' => 'dropdown-menu-end',
                        ])
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">

                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Kuda</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jenis Kuda</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Gender</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Peternakan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Harga</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ditambahkan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($kuda as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <img
                                                src="{{ asset('material/img/sendiri/horseshoe_hitam.png') }}"
                                                class="me-2 my-auto"
                                                style="width:18px; height:18px;"
                                            >

                                            <div>
                                                <h6 class="mb-0 text-sm">{{ $item->nama_kuda }}</h6>
                                                <p class="text-xs text-secondary mb-0">ID: {{ $item->id_kuda }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">
                                            {{ $item->jenis_kuda ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="text-center">
                                        @php
                                            $genderBadge = $item->gender === 'jantan' ? 'info' : 'warning';
                                        @endphp
                                        <span class="badge badge-sm bg-gradient-{{ $genderBadge }}">
                                            {{ ucfirst($item->gender ?? '-') }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <span class="text-xs font-weight-bold">
                                            {{ $item->peternakan->nama_peternakan ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @php
                                            $badge = match($item->status_jual) {
                                                'tersedia' => 'success',
                                                'terjual' => 'danger',
                                                'breeding' => 'info',
                                                default => 'secondary',
                                            };
                                        @endphp

                                        <span class="badge badge-sm bg-gradient-{{ $badge }}">
                                            {{ ucfirst($item->status_jual) }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        Rp {{ number_format($item->harga_buka ?? 0, 0, ',', '.') }}
                                    </td>

                                    <td class="text-center">
                                        <span class="text-xs text-secondary">
                                            {{ $item->created_at ? $item->created_at->format('d M Y H:i') : '-' }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <button
                                            class="btn btn-sm bg-gradient-dark mb-0"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailMarketplace{{ $item->id_kuda }}">
                                            View Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-sm py-4">
                                        Belum ada data kuda di marketplace
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@foreach($kuda as $item)

    <div class="modal fade" id="detailMarketplace{{ $item->id_kuda }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Detail Kuda - {{ $item->nama_kuda }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <h6>Informasi Kuda</h6>

                    <p class="text-sm mb-1"><strong>Nama:</strong> {{ $item->nama_kuda }}</p>
                    <p class="text-sm mb-1"><strong>Jenis:</strong> {{ $item->jenis_kuda ?? '-' }}</p>
                    <p class="text-sm mb-1"><strong>Gender:</strong> {{ ucfirst($item->gender ?? '-') }}</p>
                    <p class="text-sm mb-1"><strong>Status:</strong> {{ ucfirst($item->status_jual) }}</p>
                    <p class="text-sm mb-1"><strong>Harga:</strong> Rp {{ number_format($item->harga_buka ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm mb-3"><strong>Peternakan:</strong> {{ $item->peternakan->nama_peternakan ?? '-' }}</p>

                    <hr>

                    <h6>Detail Lisensi</h6>

                    @if($item->lisensi)
                        <p class="text-sm mb-1"><strong>Nomor Sertifikat:</strong> {{ $item->lisensi->nomor_sertifikat }}</p>
                        <p class="text-sm mb-1"><strong>Penerbit:</strong> {{ $item->lisensi->penerbit ?? '-' }}</p>
                        <p class="text-sm mb-1"><strong>Tanggal Terbit:</strong> {{ $item->lisensi->tgl_terbit ?? '-' }}</p>
                        <p class="text-sm mb-1"><strong>Masa Berlaku:</strong> {{ $item->lisensi->masa_berlaku ?? '-' }}</p>
                        <p class="text-sm mb-1"><strong>Keaslian Ras:</strong> {{ $item->lisensi->keaslian_ras ?? '-' }}</p>
                        <p class="text-sm mb-0"><strong>Riwayat Kesehatan:</strong><br>{{ $item->lisensi->riwayat_kesehatan ?? '-' }}</p>
                    @else
                        <p class="text-sm text-secondary">Lisensi belum tersedia.</p>
                    @endif

                    @if(auth()->user()->role === 'pembeli' && $page === 'tersedia' && $item->status_jual === 'tersedia')

                        <hr>

                        <h6>Pembelian Kuda</h6>

                        <form action="{{ route('transaksi.store') }}" method="POST">
                            @csrf

                            <input type="hidden" name="id_kuda" value="{{ $item->id_kuda }}">

                            @if($item->lisensi)
                                <label class="form-label">Pilihan Pembelian</label>

                                <select name="pakai_lisensi" class="form-control" required>
                                    <option value="">Pilih Opsi Pembelian</option>
                                    <option value="1">Beli dengan lisensi</option>
                                    <option value="0">Beli tanpa lisensi</option>
                                </select>
                            @else
                                <input type="hidden" name="pakai_lisensi" value="0">
                            @endif

                            <button type="submit" class="btn bg-gradient-success mt-3 mb-0">
                                Ajukan Pembelian
                            </button>
                        </form>

                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
@endforeach

<style>
    .modal {
        z-index: 99999 !important;
    }

    .modal-backdrop {
        z-index: 99998 !important;
    }

    .modal-dialog {
        margin-left: auto !important;
        margin-right: auto !important;
    }

    .modal.fade .modal-dialog {
        transform: scale(0.95);
        transition: all 0.2s ease;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }
</style>

@endsection
