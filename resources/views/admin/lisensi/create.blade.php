@extends('layouts.material')

@section('title', 'Ajukan Lisensi')
@section('breadcrumb', 'Ajukan Lisensi')

@section('content')
<div class="row mt-4">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header pb-0">
        <h6>Ajukan Lisensi Kuda</h6>
        <p class="text-sm mb-0">Isi data lisensi dan tunggu persetujuan admin</p>
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

        <form action="{{ route('lisensi.store') }}" method="POST">
          @csrf

          <label class="form-label">Kuda <span class="text-danger">*</span></label>
          <div class="input-group input-group-outline mb-3">
            <select name="id_kuda" class="form-control" required>
              <option value="">Pilih Kuda</option>
              @foreach($kudaList as $kuda)
                <option value="{{ $kuda->id_kuda }}"
                  {{ (old('id_kuda', $selectedKuda) == $kuda->id_kuda) ? 'selected' : '' }}>
                  {{ $kuda->nama_kuda }} — {{ $kuda->peternakan->nama_peternakan ?? 'tanpa peternakan' }}
                </option>
              @endforeach
            </select>
          </div>

          <label class="form-label">Nomor Sertifikat <span class="text-danger">*</span></label>
          <div class="input-group input-group-outline mb-3">
            <input type="text"
                   name="nomor_sertifikat"
                   class="form-control"
                   placeholder="Contoh: LIS-KUDA-2026-001"
                   value="{{ old('nomor_sertifikat') }}"
                   required>
          </div>

          <label class="form-label">Penerbit</label>
          <div class="input-group input-group-outline mb-3">
            <input type="text"
                   name="penerbit"
                   class="form-control"
                   placeholder="Nama institusi penerbit"
                   value="{{ old('penerbit') }}">
          </div>

          <div class="row">
            <div class="col-md-6">
              <label class="form-label">Tanggal Terbit</label>
              <div class="input-group input-group-outline mb-3">
                <input type="date"
                       name="tgl_terbit"
                       class="form-control"
                       value="{{ old('tgl_terbit') }}">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Masa Berlaku</label>
              <div class="input-group input-group-outline mb-3">
                <input type="date"
                       name="masa_berlaku"
                       class="form-control"
                       value="{{ old('masa_berlaku') }}">
              </div>
            </div>
          </div>

          <label class="form-label">Keaslian Ras</label>
          <div class="input-group input-group-outline mb-3">
            <input type="text"
                   name="keaslian_ras"
                   class="form-control"
                   placeholder="Contoh: Thoroughbred Murni"
                   value="{{ old('keaslian_ras') }}">
          </div>

          <label class="form-label">Riwayat Kesehatan</label>
          <div class="input-group input-group-outline mb-3">
            <textarea name="riwayat_kesehatan"
                      class="form-control"
                      rows="3"
                      placeholder="Deskripsi kondisi dan riwayat kesehatan kuda...">{{ old('riwayat_kesehatan') }}</textarea>
          </div>

          <div class="alert alert-light border text-sm mb-3">
            <i class="material-symbols-rounded text-warning" style="font-size:16px;vertical-align:middle;">info</i>
            Pengajuan lisensi akan berstatus <strong>Pending</strong> hingga disetujui oleh admin.
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('lisensi.index') }}" class="btn btn-light mb-0">Batal</a>
            <button type="submit" class="btn bg-gradient-dark mb-0">Kirim Pengajuan</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection
