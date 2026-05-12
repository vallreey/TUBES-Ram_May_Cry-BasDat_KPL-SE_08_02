@extends('layouts.material')

@section('title', 'Data Kuda')
@section('breadcrumb', 'Data Kuda')

@section('content')

<div class="row mt-4">
    <div class="col-12">

        <div class="card">

            {{-- HEADER --}}
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">

                <div>
                    <h6>Data Kuda</h6>

                    <p class="text-sm mb-0">
                        Data kuda dan detail lisensi
                    </p>
                </div>

                {{-- FILTER PAGE --}}
                <div>

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
                        Kuda Breeding
                    </a>

                </div>

            </div>

            {{-- BODY --}}
            <div class="card-body px-0 pb-2">

                <div class="table-responsive">

                    <table class="table align-items-center mb-0">

                        <thead>

                            <tr>

                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Kuda
                                </th>

                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Jenis
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Peternakan
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Status
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Harga
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Detail
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($kuda as $item)

                                <tr>

                                    {{-- KUDA --}}
                                    <td>

                                        <div class="d-flex px-2 py-1">

                                            <img
                                                src="{{ asset('material/img/sendiri/horseshoe_hitam.png') }}"
                                                class="me-2 my-auto"
                                                style="width:18px; height:18px;"
                                            >

                                            <div class="d-flex flex-column justify-content-center">

                                                <h6 class="mb-0 text-sm">
                                                    {{ $item->nama_kuda }}
                                                </h6>

                                                <p class="text-xs text-secondary mb-0">
                                                    ID: {{ $item->id_kuda }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>

                                    {{-- JENIS --}}
                                    <td>

                                        <p class="text-sm font-weight-bold mb-0">
                                            {{ $item->jenis_kuda ?? '-' }}
                                        </p>

                                    </td>

                                    {{-- PETERNAKAN --}}
                                    <td class="align-middle text-center">

                                        <span class="text-xs font-weight-bold">
                                            {{ $item->peternakan->nama_peternakan ?? '-' }}
                                        </span>

                                    </td>

                                    {{-- STATUS --}}
                                    <td class="align-middle text-center">

                                        @php

                                            // Jika pembeli melihat kuda miliknya,
                                            // tampilkan sebagai tersedia

                                            if (
                                                $page === 'owned'
                                                && auth()->user()->role === 'pembeli'
                                            ) {

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

                                    {{-- HARGA --}}
                                    <td class="align-middle text-center">

                                        <span class="text-xs font-weight-bold">
                                            Rp {{ number_format($item->harga_buka ?? 0, 0, ',', '.') }}
                                        </span>

                                    </td>

                                    {{-- DETAIL --}}
                                    <td class="align-middle text-center">

                                        <button
                                            class="btn btn-sm bg-gradient-dark mb-0"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailKuda{{ $item->id_kuda }}"
                                        >
                                            View Detail
                                        </button>

                                    </td>

                                </tr>

                                {{-- MODAL DETAIL --}}
                                <div
                                    class="modal fade"
                                    id="detailKuda{{ $item->id_kuda }}"
                                    tabindex="-1"
                                >

                                    <div class="modal-dialog modal-lg modal-dialog-centered">

                                        <div class="modal-content">

                                            {{-- HEADER --}}
                                            <div class="modal-header">

                                                <h5 class="modal-title">

                                                    Detail Kuda -
                                                    {{ $item->nama_kuda }}

                                                </h5>

                                                <button
                                                    type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"
                                                ></button>

                                            </div>

                                            {{-- BODY --}}
                                            <div class="modal-body">

                                                <h6>Informasi Kuda</h6>

                                                <p class="text-sm mb-1">
                                                    <strong>Nama:</strong>
                                                    {{ $item->nama_kuda }}
                                                </p>

                                                <p class="text-sm mb-1">
                                                    <strong>Jenis:</strong>
                                                    {{ $item->jenis_kuda ?? '-' }}
                                                </p>

                                                <p class="text-sm mb-1">
                                                    <strong>Status:</strong>
                                                    {{ ucfirst($item->status_jual) }}
                                                </p>

                                                <p class="text-sm mb-1">
                                                    <strong>Harga:</strong>
                                                    Rp {{ number_format($item->harga_buka ?? 0, 0, ',', '.') }}
                                                </p>

                                                <p class="text-sm mb-3">
                                                    <strong>Peternakan:</strong>
                                                    {{ $item->peternakan->nama_peternakan ?? '-' }}
                                                </p>

                                                <hr>

                                                <h6>Detail Lisensi</h6>

                                                @if($item->lisensi)

                                                    <p class="text-sm mb-1">

                                                        <strong>Nomor Sertifikat:</strong>

                                                        {{ $item->lisensi->nomor_sertifikat }}

                                                    </p>

                                                    <p class="text-sm mb-1">

                                                        <strong>Penerbit:</strong>

                                                        {{ $item->lisensi->penerbit ?? '-' }}

                                                    </p>

                                                    <p class="text-sm mb-1">

                                                        <strong>Tanggal Terbit:</strong>

                                                        {{ $item->lisensi->tgl_terbit ?? '-' }}

                                                    </p>

                                                    <p class="text-sm mb-1">

                                                        <strong>Masa Berlaku:</strong>

                                                        {{ $item->lisensi->masa_berlaku ?? '-' }}

                                                    </p>

                                                    <p class="text-sm mb-1">

                                                        <strong>Keaslian Ras:</strong>

                                                        {{ $item->lisensi->keaslian_ras ?? '-' }}

                                                    </p>

                                                    <p class="text-sm mb-0">

                                                        <strong>Riwayat Kesehatan:</strong><br>

                                                        {{ $item->lisensi->riwayat_kesehatan ?? '-' }}

                                                    </p>

                                                @else

                                                    <p class="text-sm text-secondary">
                                                        Lisensi belum tersedia.
                                                    </p>

                                                @endif

                                            </div>

                                            {{-- FOOTER --}}
                                            <div class="modal-footer">

                                                <button
                                                    type="button"
                                                    class="btn bg-gradient-secondary"
                                                    data-bs-dismiss="modal"
                                                >
                                                    Tutup
                                                </button>

                                            </div>

                                        </div>

                                    </div>

                                </div>

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

@endsection
