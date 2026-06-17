@extends('layouts.material')

@section('title', 'Kelola User')
@section('breadcrumb', 'Kelola User')

@section('content')
<div class="row mt-4">
  <div class="col-lg-12">
    <div class="card">

      <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6>Daftar User</h6>
            <p class="text-sm mb-0">Kelola semua akun pengguna sistem</p>
          </div>
          <a href="{{ route('users.create') }}" class="btn bg-gradient-dark btn-sm mb-0">
            + Tambah User
          </a>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success mx-4 mt-3 mb-0 text-sm">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger mx-4 mt-3 mb-0 text-sm">{{ session('error') }}</div>
      @endif

      <div class="card-body px-0 pb-2">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kontak</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Terdaftar</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div class="d-flex align-items-center justify-content-center bg-gradient-dark rounded-circle me-3"
                           style="width:36px; height:36px; flex-shrink:0;">
                        <span class="text-white text-sm font-weight-bold">
                          {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                        </span>
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">
                          {{ $user->nama_lengkap }}
                          @if($user->id_user === auth()->user()->id_user)
                            <span class="badge bg-gradient-secondary ms-1" style="font-size:9px;">Anda</span>
                          @endif
                        </h6>
                        <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                      </div>
                    </div>
                  </td>

                  <td>
                    <p class="text-sm mb-0">{{ $user->no_telp ?? '-' }}</p>
                    <p class="text-xs text-secondary mb-0">{{ Str::limit($user->alamat, 30) ?? '-' }}</p>
                  </td>

                  <td class="text-center">
                    @php
                      $roleBadge = match($user->role) {
                        'admin'    => 'dark',
                        'peternak' => 'info',
                        default    => 'success',
                      };
                    @endphp
                    <span class="badge badge-sm bg-gradient-{{ $roleBadge }}">
                      {{ ucfirst($user->role) }}
                    </span>
                  </td>

                  <td class="text-center">
                    <span class="text-xs text-secondary">
                      {{ $user->created_at?->format('d M Y') ?? '-' }}
                    </span>
                  </td>

                  <td class="text-center">
                    <a href="{{ route('users.edit', $user->id_user) }}"
                       class="btn btn-sm bg-gradient-dark mb-0">
                      Edit
                    </a>

                    @if($user->id_user !== auth()->user()->id_user)
                      <button class="btn btn-sm btn-outline-danger mb-0"
                              data-bs-toggle="modal"
                              data-bs-target="#hapusUser{{ $user->id_user }}">
                        Hapus
                      </button>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-sm py-4">Belum ada data user</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- Modal Hapus --}}
@foreach($users as $user)
  @if($user->id_user !== auth()->user()->id_user)
    <div class="modal fade" id="hapusUser{{ $user->id_user }}" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Hapus User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-sm">
              Hapus akun <strong>{{ $user->nama_lengkap }}</strong> ({{ $user->email }})?
              Tindakan ini tidak bisa dibatalkan.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <form action="{{ route('users.destroy', $user->id_user) }}" method="POST">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn bg-gradient-danger">Ya, Hapus</button>
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
