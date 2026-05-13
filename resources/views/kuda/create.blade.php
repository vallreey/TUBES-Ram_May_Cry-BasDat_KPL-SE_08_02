@extends('layouts.material')

@section('title', 'Tambah Kuda')
@section('breadcrumb', 'Tambah Kuda')

@section('content')
<div class="row mt-4">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h6>Tambah Kuda Baru</h6>
      </div>
      <div class="card-body">
        <form action="{{ route('kuda.store') }}" method="POST">
          @csrf

          <label class="form-label">Nama Kuda</label>
          <div class="input-group input-group-outline mb-3">
            <input type="text"
                   name="nama_kuda"
                   class="form-control"
                   placeholder="Nama Kuda"
                   value="{{ old('nama_kuda') }}"
                   required>
          </div>

          <label class="form-label">Jenis Kuda</label>
          <div class="input-group input-group-outline mb-3">
            <input type="text"
                   name="jenis_kuda"
                   class="form-control"
                   placeholder="Jenis Kuda"
                   value="{{ old('jenis_kuda') }}"
                   required>
          </div>

          <label class="form-label">Status Jual</label>
          <div class="input-group input-group-outline mb-3">
            <select name="status_jual" class="form-control" required>
              <option value="">Pilih Status Jual</option>
              <option value="tersedia" {{ old('status_jual') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
              <option value="terjual" {{ old('status_jual') == 'terjual' ? 'selected' : '' }}>Terjual</option>
            </select>
          </div>

          <label class="form-label">Harga Buka</label>
          <div class="input-group input-group-outline mb-3">
            <input type="number"
                   name="harga_buka"
                   class="form-control"
                   placeholder="Harga Buka"
                   value="{{ old('harga_buka') }}"
                   required>
          </div>

          <label class="form-label">ID Ibu</label>
          <div class="input-group input-group-outline mb-3">
            <input type="number"
                   name="id_ibu"
                   class="form-control"
                   placeholder="ID Ibu"
                   value="{{ old('id_ibu') }}">
          </div>

          <label class="form-label">ID Ayah</label>
          <div class="input-group input-group-outline mb-3">
            <input type="number"
                   name="id_ayah"
                   class="form-control"
                   placeholder="ID Ayah"
                   value="{{ old('id_ayah') }}">
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('kuda.index') }}" class="btn btn-light mb-0">
              Batal
            </a>

            <button type="submit" class="btn bg-gradient-dark mb-0">
              Simpan
            </button>
          </div>

          @php
          $statusBadge = [
              'pending' => 'warning',
              'dibayar' => 'primary',
              'selesai' => 'success',
              'dibatalkan' => 'danger',
          ];
          @endphp

          <span class="badge bg-{{ $statusBadge[$transaksi->status] }}">
            {{ $transaksi->status }}
          </span>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection
