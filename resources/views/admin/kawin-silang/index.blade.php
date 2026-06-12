@extends('layouts.material')

@section('title', 'Kawin Silang')
@section('breadcrumb', 'Kawin Silang')

@section('content')

@php
    $user = auth()->user();
@endphp

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

                @if($user->role !== 'admin')
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
                                    Penawaran
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
                                                onerror="this.style.display='none'"
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
                                        @if($b->penawaran)
                                            <span class="text-xs font-weight-bold">
                                                Rp {{ number_format($b->penawaran->harga_nego ?? $b->penawaran->harga_ditawarkan, 0, ',', '.') }}
                                            </span>

                                            <p class="text-xs text-secondary mb-0">
                                                {{ $b->penawaran->pakai_lisensi ? 'Dengan Lisensi' : 'Tanpa Lisensi' }}
                                            </p>
                                        @else
                                            <span class="text-xs text-secondary">
                                                Belum ada
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @php
                                            $badge = match($b->status_hasil) {
                                                'pending'                   => 'warning',
                                                'penawaran'                 => 'primary',
                                                'proses'                    => 'info',
                                                'menunggu_konfirmasi_anak'  => 'warning',
                                                'berhasil'                  => 'success',
                                                'gagal'                     => 'danger',
                                                'ditolak'                   => 'danger',
                                                default                     => 'secondary',
                                            };

                                            $labelStatus = match($b->status_hasil) {
                                                'menunggu_konfirmasi_anak' => 'Menunggu Konfirmasi Anak',
                                                default => ucfirst($b->status_hasil),
                                            };
                                        @endphp

                                        <span class="badge badge-sm bg-gradient-{{ $badge }}">
                                            {{ $labelStatus }}
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
                                    <td colspan="6" class="text-center text-sm py-4">
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
    @php
        $idPihakTerkait = $b->pengajuan_sebagai === 'betina'
            ? $b->id_pemilik_jantan
            : $b->id_pemilik_betina;

        $isPenerimaPengajuan = $user->id_user === $idPihakTerkait;
        $isPengaju = $user->id_user === $b->id_pengaju;

        $bolehKelolaHasil =
            $user->role === 'admin'
            || $isPenerimaPengajuan;
    @endphp

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
                        {{ $labelStatus ?? ucfirst($b->status_hasil) }}
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

                    <hr>

                    <h6>Informasi Penawaran</h6>

                    @if($b->penawaran)
                        <p class="text-sm mb-1">
                            <strong>Penawar:</strong>
                            {{ $b->penawaran->penawar->nama_lengkap ?? '-' }}
                        </p>

                        <p class="text-sm mb-1">
                            <strong>Penerima Tawaran:</strong>
                            {{ $b->penawaran->penerimaTawaran->nama_lengkap ?? '-' }}
                        </p>

                        <p class="text-sm mb-1">
                            <strong>Harga Ditawarkan:</strong>
                            Rp {{ number_format($b->penawaran->harga_ditawarkan ?? 0, 0, ',', '.') }}
                        </p>

                        @if($b->penawaran->harga_nego)
                            <p class="text-sm mb-1">
                                <strong>Harga Nego:</strong>
                                Rp {{ number_format($b->penawaran->harga_nego ?? 0, 0, ',', '.') }}
                            </p>
                        @endif

                        <p class="text-sm mb-1">
                            <strong>Lisensi:</strong>
                            {{ $b->penawaran->pakai_lisensi ? 'Dengan Lisensi' : 'Tanpa Lisensi' }}
                        </p>

                        <p class="text-sm mb-1">
                            <strong>Status Penawaran:</strong>
                            {{ ucfirst($b->penawaran->status_penawaran) }}
                        </p>

                        <p class="text-sm mb-0">
                            <strong>Catatan:</strong>
                            {{ $b->penawaran->catatan ?? '-' }}
                        </p>
                    @else
                        <p class="text-sm text-secondary mb-0">
                            Belum ada penawaran harga.
                        </p>
                    @endif

                    @if($b->anak)
                        <hr>

                        <h6>Anak Hasil Breeding</h6>

                        <p class="text-sm mb-1">
                            <strong>Nama Anak:</strong>
                            {{ $b->anak->nama_kuda ?? '-' }}
                        </p>

                        <p class="text-sm mb-1">
                            <strong>Jenis:</strong>
                            {{ $b->anak->jenis_kuda ?? '-' }}
                        </p>

                        <p class="text-sm mb-1">
                            <strong>Status Anak:</strong>
                            {{ ucfirst($b->anak->status_jual ?? '-') }}
                        </p>

                        <p class="text-sm mb-0">
                            <strong>Harga:</strong>
                            Rp {{ number_format($b->anak->harga_buka ?? 0, 0, ',', '.') }}
                        </p>

                        @if($b->anak->status_jual === 'hold')
                            <div class="alert alert-warning text-white text-sm mt-3 mb-0">
                                Anak kuda masih ditahan sementara dan belum masuk ke data aktif peternakan pengaju.
                            </div>
                        @endif
                    @endif

                </div>

                <div class="modal-footer d-block">

                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-light mb-0" data-bs-dismiss="modal">
                            Tutup
                        </button>
                    </div>

                    @if($b->status_hasil === 'pending' && $isPenerimaPengajuan)
                        <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                              method="POST"
                              class="w-100">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="aksi" value="kirim_penawaran">

                            <h6 class="mb-3">Kirim Penawaran Breeding</h6>

                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <input type="number"
                                           name="harga_ditawarkan"
                                           class="form-control"
                                           placeholder="Harga penawaran"
                                           min="0"
                                           required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <select name="pakai_lisensi" class="form-control" required>
                                        <option value="1">Dengan Lisensi</option>
                                        <option value="0">Tanpa Lisensi</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <button type="submit" class="btn bg-gradient-success w-100 mb-0">
                                        Kirim Penawaran
                                    </button>
                                </div>
                            </div>

                            <textarea name="catatan"
                                      class="form-control mt-2"
                                      rows="2"
                                      placeholder="Catatan opsional"></textarea>
                        </form>
                    @endif

                    @if(
                        $b->status_hasil === 'penawaran'
                        && $b->penawaran
                        && $b->penawaran->status_penawaran === 'ditawarkan'
                        && $isPengaju
                    )
                        <div class="d-flex justify-content-end gap-2 mb-3">
                            <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                                  method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="aksi" value="tolak_penawaran">

                                <button type="submit" class="btn bg-gradient-danger mb-0">
                                    Tolak
                                </button>
                            </form>

                            <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                                  method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="aksi" value="terima_penawaran">

                                <button type="submit" class="btn bg-gradient-success mb-0">
                                    Terima
                                </button>
                            </form>
                        </div>

                        <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                              method="POST"
                              class="w-100">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="aksi" value="nego">

                            <h6 class="mb-3">Ajukan Negosiasi</h6>

                            <div class="row">
                                <div class="col-md-8 mb-2">
                                    <input type="number"
                                           name="harga_nego"
                                           class="form-control"
                                           placeholder="Ajukan harga nego"
                                           min="0"
                                           required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <button type="submit" class="btn bg-gradient-info w-100 mb-0">
                                        Ajukan Nego
                                    </button>
                                </div>
                            </div>

                            <textarea name="catatan"
                                      class="form-control mt-2"
                                      rows="2"
                                      placeholder="Catatan negosiasi"></textarea>
                        </form>
                    @endif

                    @if(
                        $b->status_hasil === 'penawaran'
                        && $b->penawaran
                        && $b->penawaran->status_penawaran === 'nego'
                        && $isPenerimaPengajuan
                    )
                        <div class="d-flex justify-content-end gap-2">
                            <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                                  method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="aksi" value="tolak_nego">

                                <button type="submit" class="btn bg-gradient-danger mb-0">
                                    Tolak Nego
                                </button>
                            </form>

                            <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                                  method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="aksi" value="terima_nego">

                                <button type="submit" class="btn bg-gradient-success mb-0">
                                    Terima Nego
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($b->status_hasil === 'proses' && $bolehKelolaHasil)
                        <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                              method="POST"
                              class="w-100 mb-3">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="aksi" value="selesai_breeding">

                            <h6 class="mb-3">Data Anak Hasil Breeding</h6>

                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <input type="text"
                                           name="nama_anak"
                                           class="form-control"
                                           placeholder="Nama anak kuda">
                                </div>

                                <div class="col-md-4 mb-2">
                                    <input type="text"
                                           name="jenis_kuda"
                                           class="form-control"
                                           placeholder="Jenis kuda">
                                </div>

                                <div class="col-md-4 mb-2">
                                    <input type="date"
                                           name="perkiraan_kelahiran"
                                           class="form-control">
                                </div>
                            </div>

                            <button type="submit" class="btn bg-gradient-success w-100 mb-0 mt-2">
                                Tandai Selesai dan Hold Anak
                            </button>
                        </form>

                        <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                              method="POST"
                              class="d-flex justify-content-end">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="aksi" value="gagal">

                            <button type="submit" class="btn bg-gradient-danger mb-0">
                                Tandai Gagal
                            </button>
                        </form>
                    @endif

                    @if($b->status_hasil === 'menunggu_konfirmasi_anak' && $isPengaju)
                        <form action="{{ route('kawin-silang.update', $b->id_breeding) }}"
                              method="POST"
                              class="d-flex justify-content-end">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="aksi" value="konfirmasi_anak">

                            <button type="submit" class="btn bg-gradient-success mb-0">
                                Terima Anak Kuda
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

    .modal-footer .form-control {
        border: 1px solid #d2d6da;
        padding: 0.625rem 0.75rem;
    }

    .gap-2 {
        gap: 0.5rem;
    }
</style>

@endsection
