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
                    Seluruh transaksi jual beli kuda
                </p>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">

                    <table class="table align-items-center mb-0">

                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Kuda
                                </th>

                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Pembeli
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Harga
                                </th>

                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Status
                                </th>
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

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center py-4">
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

@if(auth()->user()->role === 'admin')
    Seluruh transaksi jual beli kuda
@elseif(auth()->user()->role === 'pembeli')
    Transaksi pembelian saya
@elseif(auth()->user()->role === 'peternak')
    Transaksi penjualan saya
@endif

@endsection
