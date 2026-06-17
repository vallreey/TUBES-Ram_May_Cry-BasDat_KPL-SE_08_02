@extends('layouts.material')

@section('title', 'Transaksi')
@section('breadcrumb', 'Transaksi')

@section('content')

<div class="row mt-4">
    <div class="col-12">
        <div class="card mb-4">

            <div class="card-header pb-0">
                <h6>Data Transaksi</h6>
                <p class="text-sm mb-0">
                    @if(auth()->user()->role === 'admin')
                        Seluruh transaksi jual beli kuda
                    @elseif(auth()->user()->role === 'pembeli')
                        Transaksi pembelian saya
                    @elseif(auth()->user()->role === 'peternak')
                        Transaksi penjualan saya
                    @endif
                </p>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">

                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kuda</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pembeli</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Harga</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($transaksi as $t)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <img
                                                src="{{ asset('material/img/sendiri/horseshoe_putih.png') }}"
                                                class="me-2 my-auto"
                                                style="width:18px; height:18px;"
                                            >

                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">
                                                    {{ $t->kuda->nama_kuda ?? '-' }}
                                                </h6>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">
                                            {{ $t->pembeli->nama_lengkap ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">
                                            Rp {{ number_format($t->harga_final, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    <td class="align-middle text-center">
                                        @php
                                            $badge = match($t->status_transaksi) {
                                                'selesai'    => 'success',
                                                'proses'     => 'info',
                                                'pending'    => 'warning',
                                                'dibatalkan' => 'danger',
                                                default      => 'secondary',
                                            };
                                        @endphp

                                        <span class="badge badge-sm bg-gradient-{{ $badge }}">
                                            {{ ucfirst($t->status_transaksi) }}
                                        </span>
                                    </td>

                                    <td class="align-middle text-center">
                                        <button
                                            class="btn btn-sm bg-gradient-dark mb-0"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailTransaksi{{ $t->id_transaksi }}">
                                            View Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        Belum ada transaksi
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

@foreach($transaksi as $t)
<div class="modal fade" id="detailTransaksi{{ $t->id_transaksi }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="margin-left:auto; margin-right:auto;">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Detail Transaksi #{{ $t->id_transaksi }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <h6>Data Pembelian</h6>

                <p class="text-sm mb-1"><strong>Kuda:</strong> {{ $t->kuda->nama_kuda ?? '-' }}</p>
                <p class="text-sm mb-1"><strong>Pembeli:</strong> {{ $t->pembeli->nama_lengkap ?? '-' }}</p>
                <p class="text-sm mb-1"><strong>Penjual/Peternak:</strong> {{ $t->penjual->nama_lengkap ?? '-' }}</p>
                <p class="text-sm mb-1"><strong>Harga:</strong> Rp {{ number_format($t->harga_final ?? 0, 0, ',', '.') }}</p>
                <p class="text-sm mb-1"><strong>Status:</strong> {{ ucfirst($t->status_transaksi) }}</p>

                <hr>

                <h6>Informasi Lisensi</h6>

                @if($t->id_lisensi)
                    <p class="text-sm mb-1"><strong>Pembelian:</strong> Dengan Lisensi</p>
                    <p class="text-sm mb-1"><strong>Nomor Sertifikat:</strong> {{ $t->lisensi->nomor_sertifikat ?? '-' }}</p>
                    <p class="text-sm mb-1"><strong>Penerbit:</strong> {{ $t->lisensi->penerbit ?? '-' }}</p>
                    <p class="text-xs text-success mb-0">
                        Nama kuda bisa diubah karena pembeli membeli lisensi resmi.
                    </p>
                @else
                    <p class="text-sm mb-1"><strong>Pembelian:</strong> Tanpa Lisensi</p>
                    <p class="text-xs text-secondary mb-0">
                        Nama kuda terkunci karena pembeli tidak membeli lisensi.
                    </p>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Tutup
                </button>

                @if(
                    auth()->user()->role === 'peternak'
                    && $t->id_penjual === auth()->user()->id_user
                    && $t->status_transaksi === 'pending'
                )
                    <form action="{{ route('transaksi.update', $t->id_transaksi) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="aksi" value="tolak">

                        <button type="submit" class="btn bg-gradient-danger">
                            Tolak Transaksi
                        </button>
                    </form>

                    <form action="{{ route('transaksi.update', $t->id_transaksi) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="aksi" value="terima">

                        <button type="submit" class="btn bg-gradient-success">
                            Terima Transaksi
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
