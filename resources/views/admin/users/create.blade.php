@extends('layouts.material')

@section('title', 'Tambah User')
@section('breadcrumb', 'Tambah User')

@section('content')
<div class="row mt-4">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header pb-0">
        <h6>Tambah User Baru</h6>
        <p class="text-sm mb-0">Buat akun baru untuk peternak, pembeli, atau admin</p>
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

        <form action="{{ route('users.store') }}" method="POST">
          @csrf

          <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
          <div class="input-group input-group-outline mb-3">
            <input type="text"
                   name="nama_lengkap"
                   class="form-control"
                   placeholder="Nama lengkap user"
                   value="{{ old('nama_lengkap') }}"
                   required>
          </div>

          <label class="form-label">Email <span class="text-danger">*</span></label>
          <div class="input-group input-group-outline mb-3">
            <input type="email"
                   name="email"
                   class="form-control"
                   placeholder="email@contoh.com"
                   value="{{ old('email') }}"
                   required>
          </div>

          <label class="form-label">No. Telepon</label>
          <div class="input-group input-group-outline mb-3">
            <input type="text"
                   name="no_telp"
                   class="form-control"
                   placeholder="08xxxxxxxxxx"
                   value="{{ old('no_telp') }}">
          </div>

          <label class="form-label">Alamat</label>
          <div class="input-group input-group-outline mb-3">
            <textarea name="alamat"
                      class="form-control"
                      rows="2"
                      placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
          </div>

          <label class="form-label">Role <span class="text-danger">*</span></label>
          <div class="input-group input-group-outline mb-3">
            <select name="role" class="form-control" required>
              <option value="">Pilih Role</option>
              <option value="pembeli"  {{ old('role') == 'pembeli'  ? 'selected' : '' }}>Pembeli</option>
              <option value="peternak" {{ old('role') == 'peternak' ? 'selected' : '' }}>Peternak</option>
              <option value="admin"    {{ old('role') == 'admin'    ? 'selected' : '' }}>Admin</option>
            </select>
          </div>

          <hr class="horizontal dark my-3">

          <label class="form-label">Password <span class="text-danger">*</span></label>
          <div class="input-group input-group-outline mb-3">
            <input type="password"
                   name="password"
                   class="form-control"
                   placeholder="Minimal 8 karakter"
                   required>
          </div>

          <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
          <div class="input-group input-group-outline mb-3">
            <input type="password"
                   name="password_confirmation"
                   class="form-control"
                   placeholder="Ulangi password"
                   required>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-light mb-0">Batal</a>
            <button type="submit" class="btn bg-gradient-dark mb-0">Simpan User</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection
