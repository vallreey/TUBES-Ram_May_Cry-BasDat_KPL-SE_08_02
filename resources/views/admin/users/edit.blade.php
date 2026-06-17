@extends('layouts.material')

@section('title', 'Edit User')
@section('breadcrumb', 'Edit User')

@section('content')
<div class="row mt-4">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header pb-0">
        <h6>Edit Data User</h6>
        <p class="text-sm mb-0">Ubah data akun <strong>{{ $user->nama_lengkap }}</strong></p>
      </div>
      <div class="card-body">

        @if($errors->any())
          <div class="alert alert-danger text-sm mb-3">
            <ul class="mb-0 ps-3">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger text-sm mb-3">{{ session('error') }}</div>
        @endif

        <form action="{{ route('users.update', $user->id_user) }}" method="POST">
          @csrf
          @method('PUT')

          <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
          <div class="input-group input-group-outline mb-3">
            <input type="text"
                   name="nama_lengkap"
                   class="form-control"
                   value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                   required>
          </div>

          <label class="form-label">Email <span class="text-danger">*</span></label>
          <div class="input-group input-group-outline mb-3">
            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email', $user->email) }}"
                   required>
          </div>

          <label class="form-label">No. Telepon</label>
          <div class="input-group input-group-outline mb-3">
            <input type="text"
                   name="no_telp"
                   class="form-control"
                   value="{{ old('no_telp', $user->no_telp) }}"
                   placeholder="08xxxxxxxxxx">
          </div>

          <label class="form-label">Alamat</label>
          <div class="input-group input-group-outline mb-3">
            <textarea name="alamat"
                      class="form-control"
                      rows="2"
                      placeholder="Alamat lengkap">{{ old('alamat', $user->alamat) }}</textarea>
          </div>

          <label class="form-label">Role <span class="text-danger">*</span></label>
          <div class="input-group input-group-outline mb-3">
            <select name="role" class="form-control" required
              {{ $user->id_user === auth()->user()->id_user ? 'disabled' : '' }}>
              <option value="pembeli"  {{ old('role', $user->role) == 'pembeli'  ? 'selected' : '' }}>Pembeli</option>
              <option value="peternak" {{ old('role', $user->role) == 'peternak' ? 'selected' : '' }}>Peternak</option>
              <option value="admin"    {{ old('role', $user->role) == 'admin'    ? 'selected' : '' }}>Admin</option>
            </select>
          </div>
          {{-- Jika select di-disable, value tidak terkirim — kirim via hidden --}}
          @if($user->id_user === auth()->user()->id_user)
            <input type="hidden" name="role" value="{{ $user->role }}">
            <small class="text-secondary d-block mb-3" style="margin-top:-12px;">
              Role tidak bisa diubah untuk akun Anda sendiri.
            </small>
          @endif

          <hr class="horizontal dark my-3">
          <p class="text-sm text-secondary mb-2">
            Kosongkan kolom password jika tidak ingin mengubah password.
          </p>

          <label class="form-label">Password Baru</label>
          <div class="input-group input-group-outline mb-3">
            <input type="password"
                   name="password"
                   class="form-control"
                   placeholder="Minimal 8 karakter (kosongkan jika tidak diubah)">
          </div>

          <label class="form-label">Konfirmasi Password Baru</label>
          <div class="input-group input-group-outline mb-3">
            <input type="password"
                   name="password_confirmation"
                   class="form-control"
                   placeholder="Ulangi password baru">
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-light mb-0">Batal</a>
            <button type="submit" class="btn bg-gradient-dark mb-0">Simpan Perubahan</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  {{-- Card info user --}}
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body text-center pt-4">
        <div class="d-flex align-items-center justify-content-center bg-gradient-dark rounded-circle mx-auto mb-3"
             style="width:64px; height:64px;">
          <span class="text-white h4 mb-0 font-weight-bold">
            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
          </span>
        </div>
        <h6 class="mb-0">{{ $user->nama_lengkap }}</h6>
        <p class="text-sm text-secondary mb-2">{{ $user->email }}</p>
        @php
          $roleBadge = match($user->role) {
            'admin'    => 'dark',
            'peternak' => 'info',
            default    => 'success',
          };
        @endphp
        <span class="badge badge-sm bg-gradient-{{ $roleBadge }}">{{ ucfirst($user->role) }}</span>

        <hr class="horizontal dark my-3">
        <p class="text-xs text-secondary mb-1">Terdaftar sejak</p>
        <p class="text-sm mb-0">{{ $user->created_at?->format('d M Y') ?? '-' }}</p>

        @if($user->peternakan)
          <hr class="horizontal dark my-3">
          <p class="text-xs text-secondary mb-1">Peternakan</p>
          <p class="text-sm mb-0">{{ $user->peternakan->nama_peternakan }}</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
