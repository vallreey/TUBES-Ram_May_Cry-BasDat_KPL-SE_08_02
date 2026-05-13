@extends('layouts.material')

@section('title', 'Data Kuda')
@section('breadcrumb', 'Data Kuda')

@section('content')

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Data Kuda</h6>
                        <p class="text-sm mb-0">Data kuda dan detail lisensi</p>
                    </div>

                    <div class="d-flex gap-2">

                        <a href="{{ route('kuda.index') }}"
                           class="btn btn-sm {{ $page === 'owned' ? 'bg-gradient-dark text-white' : 'btn-outline-dark' }}">
                            Kuda Dimiliki
                        </a>

                        <a href="{{ route('kuda.tersedia') }}"
                           class="btn btn-sm {{ $page === 'tersedia' ? 'bg-gradient-success text-white' : 'btn-outline-success' }}">
                            Kuda Tersedia
                        </a>

                        <a href="{{ route('kuda.terjual') }}"
                           class="btn btn-sm {{ $page === 'terjual' ? 'bg-gradient-danger text-white' : 'btn-outline-danger' }}">
                            Kuda Terjual
                        </a>

                        <a href="{{ route('kuda.breeding') }}"
                           class="btn btn-sm {{ $page === 'breeding' ? 'bg-gradient-info text-white' : 'btn-outline-info' }}">
                            Breeding
                        </a>
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
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Peternakan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Jual</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Harga Buka</th>
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
                                        <span class="text-xs font-weight-bold">
                                            {{ $item->peternakan->nama_peternakan ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @php
                                            if ($page === 'owned' && auth()->user()->role === 'pembeli') {
                                                $statusTampil = 'tersedia';
                                            } else {
                                                $statusTampil = $item->status_jual;
                                            }

                                            $badge = match($statusTampil) {
                                                'tersedia' => 'success',
                                                'terjual' => 'danger',
                                                'breeding' => 'info',
                                                default => 'secondary',
                                            };
                                        @endphp

                                        <span class="badge badge-sm bg-gradient-{{ $badge }}">
                                            {{ ucfirst($statusTampil) }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        Rp {{ number_format($item->harga_buka ?? 0, 0, ',', '.') }}
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

                                        // Admin boleh edit/hapus semua
                                        if ($user->role === 'admin') {
                                            $bolehKelola = true;
                                        }

                                        // Peternak hanya boleh edit/hapus kuda miliknya dan belum terjual
                                        elseif (
                                            $user->role === 'peternak'
                                            && $item->peternakan
                                            && $item->peternakan->id_user === $user->id_user
                                            && $item->status_jual !== 'terjual'
                                        ) {
                                            $bolehKelola = true;
                                        }

                                        // Pembeli hanya boleh edit nama jika transaksi selesai dan tanpa lisensi
                                        elseif ($user->role === 'pembeli') {
                                        $transaksiPembeli = $item->transaksi
                                            ->where('id_pembeli', $user->id_user)
                                            ->where('status_transaksi', 'selesai')
                                            ->first();

                                        if ($transaksiPembeli) {

                                            // 1. Kuda dari awal tidak punya lisensi
                                            if (!$item->lisensi) {
                                                $bolehEditNamaPembeli = true;
                                            }

                                            // 2. Kuda punya lisensi dan pembeli membeli lisensinya
                                            elseif ($item->lisensi && $transaksiPembeli->id_lisensi !== null) {
                                                $bolehEditNamaPembeli = true;
                                            }

                                            // 3. Kuda punya lisensi tapi pembeli beli tanpa lisensi
                                            else {
                                                $bolehEditNamaPembeli = false;
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
                                    <td colspan="6" class="text-center text-sm py-4">
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

    {{-- MODAL VIEW DETAIL --}}
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

                        <button type="submit" class="btn bg-gradient-success">
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
    /* MODAL DI ATAS SIDEBAR */
    .modal {
        z-index: 99999 !important;
    }

    .modal-backdrop {
        z-index: 99998 !important;
    }

    /* AGAR MODAL TIDAK KETUTUP SIDEBAR */
    .modal-dialog {
        margin-left: auto !important;
        margin-right: auto !important;
    }

    /* ANIMASI HALUS */
    .modal.fade .modal-dialog {
        transform: scale(0.95);
        transition: all 0.2s ease;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }
</style>

@endsection
