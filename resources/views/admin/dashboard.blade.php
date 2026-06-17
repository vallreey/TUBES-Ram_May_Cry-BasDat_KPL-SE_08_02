@extends('layouts.material')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

  {{-- Stat Cards --}}
  <div class="row mt-3">

    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">Total Kuda</p>
              <h4 class="mb-0">{{ $totalKuda ?? 0 }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                <img
                src="{{ asset('material/img/sendiri/horseshoe_putih.png') }}"
                style="width:24px; height:24px; margin-top:12px;"
            >
            </div>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-2 ps-3">
          <p class="mb-0 text-sm">Kuda yang dimiliki</p>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">Total Transaksi</p>
              <h4 class="mb-0">{{ $totalTransaksi ?? 0 }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">receipt_long</i>
            </div>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-2 ps-3">
          <p class="mb-0 text-sm">Transaksi jual beli kuda</p>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">Kawin Silang</p>
              <h4 class="mb-0">{{ $totalBreeding ?? 0 }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
              <img
                src="{{ asset('material/img/sendiri/Gender_putih.png') }}"
                style="width:24px; height:24px; margin-top:12px;"
                >
            </div>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-2 ps-3">
          <p class="mb-0 text-sm">Total proses breeding</p>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">Total Peternakan</p>
              <h4 class="mb-0">{{ $totalPeternakan ?? 0 }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">home</i>
            </div>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-2 ps-3">
          <p class="mb-0 text-sm">Peternakan terdaftar</p>
        </div>
      </div>
    </div>

  </div>

  {{-- Tabel Transaksi Terbaru --}}
  <div class="row mt-4">
    <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
      <div class="card">
        <div class="card-header pb-0">
          <div class="row">
            <div class="col-lg-6 col-7">
              <h6>Transaksi Terbaru</h6>
              <p class="text-sm mb-0">Data transaksi jual beli kuda</p>
            </div>
            <div class="col-lg-6 col-5 my-auto text-end">
              <a href="{{ route('transaksi.index') }}" class="btn btn-sm bg-gradient-dark mb-0">Lihat Semua</a>
            </div>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="table-responsive">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kuda</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pembeli</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Harga</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($transaksiTerbaru ?? [] as $t)
                  <tr>
                    <td>
                      <div class="d-flex px-2 py-1">
                        <img
                            src="{{ asset('material/img/sendiri/horseshoe_hitam.png') }}"
                            class="me-2 my-auto"
                            style="width:18px; height:18px;"
                        >
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ $t->kuda->nama_kuda ?? '-' }}</h6>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-sm font-weight-bold mb-0">{{ $t->pembeli->nama_lengkap ?? '-' }}</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="text-xs font-weight-bold">Rp {{ number_format($t->harga_final, 0, ',', '.') }}</span>
                    </td>
                    <td class="align-middle text-center">
                @php
                    // AUTOMATA TRANSAKSI

                    $status = $t->status_transaksi;

                    $automata = match($status) {
                    'pending' => [
                        'badge' => 'warning',
                        'icon' => 'hourglass_empty',
                        'text' => 'Pending',
                        'next' => 'Menunggu diproses'
                    ],
                    'proses' => [
                        'badge' => 'info',
                        'icon' => 'sync',
                        'text' => 'Proses',
                        'next' => 'Sedang diproses'
                    ],
                    'selesai' => [
                        'badge' => 'success',
                        'icon' => 'check_circle',
                        'text' => 'Selesai',
                        'next' => 'Transaksi selesai'
                    ],
                    'dibatalkan' => [
                        'badge' => 'danger',
                        'icon' => 'cancel',
                        'text' => 'Dibatalkan',
                        'next' => 'Transaksi dibatalkan'
                    ],
                    default => [
                        'badge' => 'secondary',
                        'icon' => 'help',
                        'text' => 'Tidak diketahui',
                        'next' => '-'
                    ],
                    };
                @endphp

                <span class="badge badge-sm bg-gradient-{{ $automata['badge'] }}">
                    <i class="material-symbols-rounded text-xs align-middle">
                    {{ $automata['icon'] }}
                    </i>
                    {{ $automata['text'] }}
                </span>

                <br>

                <small class="text-secondary text-xs">
                    {{ $automata['next'] }}
                </small>
                </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-sm py-3">Belum ada transaksi</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Kawin Silang Terbaru --}}
    <div class="col-lg-4 col-md-6">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h6>Kawin Silang Terbaru</h6>
        </div>
        <div class="card-body p-3">
          <div class="timeline timeline-one-side">
            @forelse($breedingTerbaru ?? [] as $b)
              <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-symbols-rounded text-success text-gradient">join</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">
                    {{ $b->kudaBetina->nama_kuda ?? '-' }} × {{ $b->kudaJantan->nama_kuda ?? '-' }}
                  </h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                    {{ \Carbon\Carbon::parse($b->tgl_breeding)->format('d M Y') }} &mdash;
                    <span class="text-{{ $b->status_hasil === 'berhasil' ? 'success' : ($b->status_hasil === 'gagal' ? 'danger' : 'info') }}">
                      {{ ucfirst($b->status_hasil) }}
                    </span>
                  </p>
                </div>
              </div>
            @empty
              <p class="text-sm text-secondary text-center">Belum ada data breeding</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
