@extends('layouts.material')

@section('title', 'Profile')
@section('breadcrumb', 'Profile')

@section('content')
<div class="row mt-4">
  <div class="col-lg-8 col-md-10">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex align-items-center">
          <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg me-3">
            <i class="material-symbols-rounded opacity-10">account_circle</i>
          </div>
          <div>
            <h6 class="mb-0">Edit Profile</h6>
            <p class="text-sm mb-0">Ubah username, data akun, dan password login.</p>
          </div>
        </div>
      </div>

      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success text-white" role="alert">
            {{ session('success') }}
          </div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger text-white" role="alert">
            <strong>Data belum valid.</strong>
            <ul class="mb-0 mt-2 ps-3">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-6">
              <label class="form-label">Username / Nama Lengkap</label>
              <div class="input-group input-group-outline mb-3">
                <input type="text"
                       name="nama_lengkap"
                       class="form-control"
                       placeholder="Masukkan username atau nama lengkap"
                       value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                       required>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email Login</label>
              <div class="input-group input-group-outline mb-3">
                <input type="email"
                       name="email"
                       class="form-control"
                       placeholder="Masukkan email"
                       value="{{ old('email', $user->email) }}"
                       required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label class="form-label">No. Telepon</label>
              <div class="input-group input-group-outline mb-3">
                <input type="text"
                       name="no_telp"
                       class="form-control"
                       placeholder="Masukkan nomor telepon"
                       value="{{ old('no_telp', $user->no_telp) }}">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Role</label>
              <div class="input-group input-group-outline mb-3">
                <input type="text"
                       class="form-control"
                       value="{{ ucfirst($user->role) }}"
                       disabled>
              </div>
            </div>
          </div>

          <label class="form-label">Alamat</label>
          <div class="input-group input-group-outline mb-4">
            <textarea name="alamat"
                      class="form-control"
                      rows="3"
                      placeholder="Masukkan alamat">{{ old('alamat', $user->alamat) }}</textarea>
          </div>

          <hr class="horizontal dark my-4">

          <h6 class="mb-1">Ganti Password</h6>
          <p class="text-sm text-secondary mb-3">Kosongkan bagian ini jika tidak ingin mengubah password.</p>

          <div class="row">
            <div class="col-md-4">
              <label class="form-label">Password Lama</label>
              <div class="input-group input-group-outline mb-3">
                <input type="password"
                       name="password_lama"
                       class="form-control"
                       placeholder="Password lama">
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Password Baru</label>
              <div class="input-group input-group-outline mb-3">
                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="Minimal 8 karakter">
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Konfirmasi Password Baru</label>
              <div class="input-group input-group-outline mb-3">
                <input type="password"
                       name="password_confirmation"
                       class="form-control"
                       placeholder="Ulangi password baru">
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('dashboard') }}" class="btn btn-light mb-0">
              Batal
            </a>

            <button type="submit" class="btn bg-gradient-dark mb-0">
              Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
