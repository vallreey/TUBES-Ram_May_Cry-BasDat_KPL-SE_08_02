@extends('layouts.material')

@section('title', 'Lisensi Kuda')
@section('breadcrumb', 'Lisensi')

@section('content')
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Daftar Lisensi Kuda</h6>
                        <p class="text-sm mb-0">
                            @if(auth()->user()->role === 'admin')
                                Semua pengajuan lisensi dari peternak dan pembeli
                            @elseif(auth()->user()->role === 'peternak')
                                Lisensi kuda di peternakan Anda
                            @else
                                Lisensi kuda yang Anda miliki
                            @endif
                        </p>
                    </div>

                    @if(auth()->user()->role !== 'admin')
                    <a href="{{ route('lisensi.create') }}" class="btn bg-gradient-dark btn-sm mb-0">
                        + Ajukan Lisensi
                    </a>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success mx-4 mt-3 mb-0 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mx-4 mt-3 mb-0 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card-body px-0 pb-2">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">

                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kuda</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No. Sertifikat</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pengaju</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Penerbit</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Masa Berlaku</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($lisensi as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <img src="{{ asset('material/img/sendiri/horseshoe_hitam.png') }}"
                                                 class="me-2 my-auto" style="width:18px; height:18px;">
                                            <div>
                                                <h6 class="mb-0 text-sm">{{ $item->kuda->nama_kuda ?? '-' }}</h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $item->kuda->peternakan->nama_peternakan ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $item->nomor_sertifikat }}</p>
                                    </td>

                                    <td class="text-center">
                                        <span class="text-xs">{{ $item->pengaju->nama_lengkap ?? '-' }}</span>
                                    </td>

                                    <td class="text-center">
                                        <span class="text-xs">{{ $item->penerbit ?? '-' }}</span>
                                    </td>

                                    <td class="text-center">
                                        <span class="text-xs">{{ $item->masa_berlaku ?? '-' }}</span>
                                    </td>

                                    <td class="text-center">
                                        @php
                                            $badge = match($item->status) {
                                                'approved' => 'success',
                                                'declined' => 'danger',
                                                default    => 'warning',
                                            };
                                            $label = match($item->status) {
                                                'approved' => 'Approved',
                                                'declined' => 'Declined',
                                                default    => 'Pending',
                                            };
                                        @endphp
                                        <span class="badge badge-sm bg-gradient-{{ $badge }}">
                                            {{ $label }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        {{-- Tombol Detail --}}
                                        <button class="btn btn-sm bg-gradient-dark mb-0"
                                                data-bs-toggle="modal"
                                                data-bs-target="#detailLisensi{{ $item->id_lisensi }}">
                                            Detail
                                        </button>

                                        {{-- Admin: approve/decline hanya jika pending --}}
                                        @if(auth()->user()->role === 'admin' && $item->status === 'pending')
                                            <button class="btn btn-sm bg-gradient-success mb-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#approveModal{{ $item->id_lisensi }}">
                                                Approve
                                            </button>
                                            <button class="btn btn-sm bg-gradient-danger mb-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#declineModal{{ $item->id_lisensi }}">
                                                Decline
                                            </button>
                                        @endif

                                        {{-- Pengaju bisa hapus jika masih pending --}}
                                        @if($item->id_pengaju === auth()->user()->id_user && $item->status === 'pending')
                                            <button class="btn btn-sm btn-outline-danger mb-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#hapusLisensi{{ $item->id_lisensi }}">
                                                Batalkan
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-sm py-4">
                                        Belum ada data lisensi
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

{{-- MODALS --}}
@foreach($lisensi as $item)

    {{-- Modal Detail --}}
    <div class="modal fade" id="detailLisensi{{ $item->id_lisensi }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Lisensi — {{ $item->kuda->nama_kuda ?? '-' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-sm mb-1"><strong>Nomor Sertifikat:</strong> {{ $item->nomor_sertifikat }}</p>
                    <p class="text-sm mb-1"><strong>Penerbit:</strong> {{ $item->penerbit ?? '-' }}</p>
                    <p class="text-sm mb-1"><strong>Tanggal Terbit:</strong> {{ $item->tgl_terbit ?? '-' }}</p>
                    <p class="text-sm mb-1"><strong>Masa Berlaku:</strong> {{ $item->masa_berlaku ?? '-' }}</p>
                    <p class="text-sm mb-1"><strong>Keaslian Ras:</strong> {{ $item->keaslian_ras ?? '-' }}</p>
                    <p class="text-sm mb-1"><strong>Riwayat Kesehatan:</strong><br>{{ $item->riwayat_kesehatan ?? '-' }}</p>
                    <hr>
                    <p class="text-sm mb-1"><strong>Pengaju:</strong> {{ $item->pengaju->nama_lengkap ?? '-' }}</p>
                    <p class="text-sm mb-1">
                        <strong>Status:</strong>
                        @php
                            $badge = match($item->status) { 'approved'=>'success','declined'=>'danger',default=>'warning' };
                            $label = match($item->status) { 'approved'=>'Approved','declined'=>'Declined',default=>'Pending' };
                        @endphp
                        <span class="badge bg-gradient-{{ $badge }}">{{ $label }}</span>
                    </p>
                    @if($item->catatan_admin)
                        <p class="text-sm mb-0"><strong>Catatan Admin:</strong><br>{{ $item->catatan_admin }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Approve (Admin) --}}
    @if(auth()->user()->role === 'admin' && $item->status === 'pending')
    <div class="modal fade" id="approveModal{{ $item->id_lisensi }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Setujui Lisensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('lisensi.approve', $item->id_lisensi) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-sm">Setujui pengajuan lisensi untuk kuda <strong>{{ $item->kuda->nama_kuda ?? '-' }}</strong>?</p>
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Catatan untuk pengaju..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn bg-gradient-success">Ya, Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="declineModal{{ $item->id_lisensi }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Lisensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('lisensi.decline', $item->id_lisensi) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-sm">Tolak pengajuan lisensi untuk kuda <strong>{{ $item->kuda->nama_kuda ?? '-' }}</strong>?</p>
                        <label class="form-label">Alasan penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Alasan penolakan..." required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn bg-gradient-danger">Tolak Lisensi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Batalkan (Pengaju) --}}
    @if($item->id_pengaju === auth()->user()->id_user && $item->status === 'pending')
    <div class="modal fade" id="hapusLisensi{{ $item->id_lisensi }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Batalkan Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-sm">Batalkan pengajuan lisensi untuk kuda <strong>{{ $item->kuda->nama_kuda ?? '-' }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                    <form action="{{ route('lisensi.destroy', $item->id_lisensi) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn bg-gradient-danger">Ya, Batalkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

@endforeach

<style>
    .modal { z-index: 99999 !important; }
    .modal-backdrop { z-index: 99998 !important; }
</style>

@endsection
