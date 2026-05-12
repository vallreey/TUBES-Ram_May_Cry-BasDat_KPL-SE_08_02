@extends('layouts.material')

@section('title', 'Peternakan')
@section('breadcrumb', 'Peternakan')

@section('content')

<div class="row mt-4">
    <div class="col-12">
        <div class="card">

            <div class="card-header pb-0">
                <h6>Data Peternakan</h6>
                <p class="text-sm mb-0">
                    List peternakan yang terdaftar di sistem
                </p>
            </div>

            <div class="card-body px-0 pb-2">
                <div class="table-responsive">

                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Peternakan
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Pemilik
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Kapasitas
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Lokasi
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Detail
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($peternakan as $p)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <i class="material-symbols-rounded text-dark me-2 my-auto">
                                                home
                                            </i>

                                            <div>
                                                <h6 class="mb-0 text-sm">
                                                    {{ $p->nama_peternakan }}
                                                </h6>

                                                <p class="text-xs text-secondary mb-0">
                                                    ID: {{ $p->id_peternakan }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="align-middle text-center">
                                        <span class="text-xs font-weight-bold">
                                            {{ $p->user->nama_lengkap ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="align-middle text-center">
                                        <span class="text-xs font-weight-bold">
                                            {{ $p->kapasitas_kandang }} kuda
                                        </span>
                                    </td>

                                    <td class="align-middle text-center">
                                        <span class="text-xs font-weight-bold">
                                            {{ $p->lokasi_map ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="align-middle text-center">
                                        <button
                                            class="btn btn-sm bg-gradient-dark mb-0"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailPeternakan{{ $p->id_peternakan }}"
                                        >
                                            View Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-sm py-4">
                                        Belum ada data peternakan
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

{{-- MODAL DETAIL PETERNAKAN --}}
@foreach($peternakan as $p)
    <div class="modal fade" id="detailPeternakan{{ $p->id_peternakan }}" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Detail Peternakan - {{ $p->nama_peternakan }}
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>
                </div>

                <div class="modal-body">

                    <h6>Informasi Peternakan</h6>

                    <p class="text-sm mb-1">
                        <strong>Nama Peternakan:</strong>
                        {{ $p->nama_peternakan }}
                    </p>

                    <p class="text-sm mb-1">
                        <strong>Pemilik:</strong>
                        {{ $p->user->nama_lengkap ?? '-' }}
                    </p>

                    <p class="text-sm mb-1">
                        <strong>Kapasitas Kandang:</strong>
                        {{ $p->kapasitas_kandang }} kuda
                    </p>

                    <p class="text-sm mb-1">
                        <strong>Lokasi:</strong>
                        {{ $p->lokasi_map ?? '-' }}
                    </p>

                    <p class="text-sm mb-3">
                        <strong>Alamat Lengkap:</strong>
                        {{ $p->alamat_lengkap ?? '-' }}
                    </p>

                    <hr>

                    <h6>List Kuda Milik Peternakan</h6>

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">

                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Kuda
                                    </th>

                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Jenis
                                    </th>

                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status
                                    </th>

                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Harga
                                    </th>

                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Lisensi
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($p->kuda as $k)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <img
                                                    src="{{ asset('material/img/sendiri/horseshoe_hitam.png') }}"
                                                    class="me-2 my-auto"
                                                    style="width:18px; height:18px;"
                                                >

                                                <div>
                                                    <h6 class="mb-0 text-sm">
                                                        {{ $k->nama_kuda }}
                                                    </h6>

                                                    <p class="text-xs text-secondary mb-0">
                                                        ID: {{ $k->id_kuda }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ $k->jenis_kuda ?? '-' }}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center">
                                            @php
                                                $badge = match($k->status_jual) {
                                                    'tersedia' => 'success',
                                                    'terjual' => 'danger',
                                                    'breeding' => 'info',
                                                    default => 'secondary',
                                                };
                                            @endphp

                                            <span class="badge badge-sm bg-gradient-{{ $badge }}">
                                                {{ ucfirst($k->status_jual) }}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                Rp {{ number_format($k->harga_buka ?? 0, 0, ',', '.') }}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center">
                                            @if($k->lisensi)
                                                <span class="badge badge-sm bg-gradient-success">
                                                    Ada
                                                </span>

                                                <p class="text-xs text-secondary mb-0 mt-1">
                                                    {{ $k->lisensi->nomor_sertifikat }}
                                                </p>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">
                                                    Belum Ada
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-sm py-3">
                                            Belum ada kuda di peternakan ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>

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
</style>

@endsection
