@extends('layouts.material')

@section('title', 'Kawin Silang')
@section('breadcrumb', 'Kawin Silang')

@section('content')

<div class="row mt-4">
    <div class="col-12">
        <div class="card">

            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6>Data Kawin Silang</h6>
                    <p class="text-sm mb-0">
                        Pengajuan kawin silang antar kuda
                    </p>
                </div>

                @if(auth()->user()->role !== 'admin')
                    <a href="{{ route('kawin-silang.create') }}"
                       class="btn btn-sm bg-gradient-dark mb-0">
                        Ajukan Kawin Silang
                    </a>
                @endif
            </div>

            <div class="card-body px-0 pb-2">
                <div class="table-responsive">

                    <table class="table align-items-center mb-0">

                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Kuda Betina / Ibu
                                </th>

                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Kuda Jantan / Ayah
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Pengaju
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Status
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($breeding as $b)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <img
                                                src="{{ asset('material/img/sendiri/Gender_hitam.png') }}"
                                                class="me-2 my-auto"
                                                style="width:18px; height:18px;"
                                            >

                                            <div>
                                                <h6 class="mb-0 text-sm">
                                                    {{ $b->kudaBetina->nama_kuda ?? '-' }}
                                                </h6>

                                                <p class="text-xs text-secondary mb-0">
                                                    Pemilik: {{ $b->pemilikBetina->nama_lengkap ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">
                                            {{ $b->kudaJantan->nama_kuda ?? '-' }}
                                        </p>

                                        <p class="text-xs text-secondary mb-0">
                                            Pemilik: {{ $b->pemilikJantan->nama_lengkap ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="text-center">
                                        <span class="text-xs font-weight-bold">
                                            {{ $b->pengaju->nama_lengkap ?? '-' }}
                                        </span>

                                        <p class="text-xs text-secondary mb-0">
                                            Sebagai: {{ ucfirst($b->pengajuan_sebagai ?? '-') }}
                                        </p>
                                    </td>

                                    <td class="text-center">
                                        @php
                                            $badge = match($b->status_hasil) {
                                                'pending'  => 'warning',
                                                'proses'   => 'info',
                                                'berhasil' => 'success',
                                                'gagal'    => 'danger',
                                                'ditolak'  => 'danger',
                                                default    => 'secondary',
                                            };
                                        @endphp

                                        <span class="badge badge-sm bg-gradient-{{ $badge }}">
                                            {{ ucfirst($b->status_hasil) }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <button
                                            class="btn btn-sm bg-gradient-dark mb-0"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailBreeding{{ $b->id_breeding }}">
                                            View Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-sm py-4">
                                        Belum ada pengajuan kawin silang
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

@foreach($breeding as $b)
    <div class="modal fade" id="detailBreeding{{ $b->id_breeding }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Detail Kawin Silang #{{ $b->id_breeding }}
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <h6>Data Pengajuan</h6>

                    <p class="text-sm mb-1">
                        <strong>Pengaju:</strong>
                        {{ $b->pengaju->nama_lengkap ?? '-' }}
                    </p>

                    <p class="text-sm mb-1">
                        <strong>Pengajuan Sebagai:</strong>
                        {{ ucfirst($b->pengajuan_sebagai ?? '-') }}
                    </p>

                    <p class="text-sm mb-1">
                        <strong>Tanggal Pengajuan:</strong>
                        {{ $b->tgl_pengajuan ?? '-' }}
                    </p>

                    <p class="text-sm mb-1">
                        <strong>Tanggal Breeding:</strong>
                        {{ $b->tgl_breeding ?? '-' }}
                    </p>

                    <p class="text-sm mb-1">
                        <strong>Status:</strong>
                        {{ ucfirst($b->status_hasil) }}
                    </p>

                    <p class="text-sm mb-3">
                        <strong>Perkiraan Kelahiran:</strong>
                        {{ $b->perkiraan_kelahiran ?? '-' }}
                    </p>

                    <hr>

                    <h6>Detail Kuda</h6>

                    <p class="text-sm mb-1">
                        <strong>Kuda Betina / Calon Ibu:</strong>
                        {{ $b->kudaBetina->nama_kuda ?? '-' }}
                    </p>

                    <p class="text-sm mb-1">
                        <strong>Pemilik Betina:</strong>
                        {{ $b->pemilikBetina->nama_lengkap ?? '-' }}
                    </p>

                    <p class="text-sm mb-1">
                        <strong>Kuda Jantan / Calon Ayah:</strong>
                        {{ $b->kudaJantan->nama_kuda ?? '-' }}
                    </p>

                    <p class="text-sm mb-1">
                        <strong>Pemilik Jantan:</strong>
                        {{ $b->pemilikJantan->nama_lengkap ?? '-' }}
                    </p>

                    <div class="alert alert-info text-white text-sm mt-3">
                        Jika nantinya dibuat anak kuda, maka
                        <strong>ID Ibu</strong> akan menggunakan
                        {{ $b->id_betina }},
                        dan <strong>ID Ayah</strong> akan menggunakan
                        {{ $b->id_jantan }}.
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">
                        Tutup
                    </button>

                    @php
                        $idPihakTerkait = $b->pengajuan_sebagai === 'betina'
                            ? $b->id_pemilik_jantan
                            : $b->id_pemilik_betina;

                        $bolehAcc =
                            $b->status_hasil === 'pending'
                            && (
                                auth()->user()->role === 'admin'
                                || auth()->user()->id_user === $idPihakTerkait
                            );

                        $bolehUpdateHasil =
                            auth()->user()->role === 'admin'
                            && $b->status_hasil === 'proses';
                    @endphp

                    @if($bolehAcc)
                        <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="aksi" value="tolak">

                            <button class="btn bg-gradient-danger">
                                Tolak Pengajuan
                            </button>
                        </form>

                        <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="aksi" value="acc">

                            <button class="btn bg-gradient-success">
                                Setujui Pengajuan
                            </button>
                        </form>
                    @endif

                    @if($bolehUpdateHasil)
                        <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="aksi" value="berhasil">

                            <button class="btn bg-gradient-success">
                                Tandai Berhasil
                            </button>
                        </form>

                        <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="aksi" value="gagal">

                            <button class="btn bg-gradient-danger">
                                Tandai Gagal
                            </button>
                        </form>
                    @endif
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
</style>

@endsection
