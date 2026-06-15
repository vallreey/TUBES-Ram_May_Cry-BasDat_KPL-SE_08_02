@extends('layouts.material')

@section('title', 'Data Kuda')
@section('breadcrumb', 'Data Kuda')

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
                        <h6 class="mb-1">Data Kuda</h6>
                        <p class="text-sm mb-0">
                            Data master kuda, peternakan, status, dan detail lisensi
                        </p>
                    </div>

                    @include('admin.partials.kuda-search-box', [
                        'action' => route('kuda.index'),
                    ])
                </div>
            </div>

            <div class="px-4 pt-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 kuda-toolbar-row pt-3">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        @include('admin.partials.kuda-filter-dropdown', [
                            'action' => route('kuda.index'),
                        ])

                        {{-- Tombol export PDF membawa search, filter, dan sort yang sedang aktif. --}}
                        <a href="{{ route('kuda.export.pdf', request()->query()) }}"
                           class="btn btn-sm bg-gradient-danger kuda-export-pdf-btn mb-0">
                            <span class="material-symbols-rounded kuda-filter-icon">picture_as_pdf</span>
                            Export PDF
                        </a>
                    </div>

                    @if($hasActiveQuery)
                        <div class="text-end">
                            <p class="text-xs text-secondary mb-1">
                                Menampilkan {{ $kuda->count() }} data berdasarkan search/filter.
                            </p>
                            <a href="{{ route('kuda.index') }}" class="btn btn-sm btn-outline-secondary mb-0">
                                Reset Semua
                            </a>
                        </div>
                    @endif
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
                                            data-bs-target="#detailKuda{{ $item->id_kuda }}">
                                            View Detail
                                        </button>

                                        @php
                                            $user = auth()->user();
                                            $bolehKelola = false;
                                            $bolehEditNamaPembeli = false;

                                            if ($user->role === 'admin') {
                                                $bolehKelola = true;
                                            } elseif (
                                                $user->role === 'peternak'
                                                && $item->peternakan
                                                && $item->peternakan->id_user === $user->id_user
                                                && $item->status_jual !== 'terjual'
                                            ) {
                                                $bolehKelola = true;
                                            } elseif ($user->role === 'pembeli') {
                                                $transaksiPembeli = $item->transaksi
                                                    ->where('id_pembeli', $user->id_user)
                                                    ->where('status_transaksi', 'selesai')
                                                    ->first();

                                                if ($transaksiPembeli) {
                                                    if (!$item->lisensi) {
                                                        $bolehEditNamaPembeli = true;
                                                    } elseif ($item->lisensi && $transaksiPembeli->id_lisensi !== null) {
                                                        $bolehEditNamaPembeli = true;
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if($bolehKelola)
                                            <a href="{{ route('kuda.edit', $item->id_kuda) }}"
                                               class="btn btn-sm btn-light mb-0">
                                                Edit
                                            </a>

                                            <button type="button"
                                                    class="btn btn-sm bg-gradient-danger mb-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#hapusKuda{{ $item->id_kuda }}">
                                                Hapus
                                            </button>
                                        @endif

                                        @if($bolehEditNamaPembeli)
                                            <a href="{{ route('kuda.edit', $item->id_kuda) }}"
                                               class="btn btn-sm bg-gradient-info mb-0">
                                                Edit Nama
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-sm py-4">
                                        Belum ada data kuda
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

    <div class="modal fade" id="detailKuda{{ $item->id_kuda }}" tabindex="-1">
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


                    @include('admin.partials.kuda-parent-info', ['kuda' => $item])

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
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="hapusKuda{{ $item->id_kuda }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="text-sm mb-0">
                        Apakah kamu yakin ingin menghapus kuda
                        <strong>{{ $item->nama_kuda }}</strong>?
                    </p>

                    <p class="text-xs text-secondary mb-0 mt-2">
                        Data yang sudah dihapus tidak bisa dikembalikan.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <form action="{{ route('kuda.destroy', $item->id_kuda) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn bg-gradient-danger">
                            Ya, Hapus
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endforeach


@if(auth()->user()->role !== 'pembeli')
    <div class="position-fixed bottom-0 end-0 m-4" style="z-index: 999;">
        <a href="{{ route('kuda.create') }}"
           class="btn bg-gradient-dark shadow-dark">
            + Tambah Kuda
        </a>
    </div>
@endif

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
