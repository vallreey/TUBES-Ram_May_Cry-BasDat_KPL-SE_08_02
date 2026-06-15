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

        @if($errors->any())
          <div class="alert alert-danger text-sm mb-3">
            <ul class="mb-0 ps-3">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

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

          <label class="form-label">Gender Kuda</label>
          <div class="input-group input-group-outline mb-3">
            <select name="gender" class="form-control" required>
              <option value="">Pilih Gender</option>
              <option value="jantan" {{ old('gender') == 'jantan' ? 'selected' : '' }}>Jantan</option>
              <option value="betina" {{ old('gender') == 'betina' ? 'selected' : '' }}>Betina</option>
            </select>
          </div>

          <label class="form-label">Status Jual</label>
          <div class="input-group input-group-outline mb-3">
            <select name="status_jual" class="form-control" required>
              <option value="">Pilih Status Jual</option>
              <option value="tersedia" {{ old('status_jual') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
              <option value="terjual"  {{ old('status_jual') == 'terjual'  ? 'selected' : '' }}>Terjual</option>
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

          <label class="form-label">Ibu</label>
          <div class="input-group input-group-outline mb-1">
            <select name="id_ibu" class="form-control">
              <option value="">Tidak dipilih</option>
              @foreach($ibuOptions as $ibu)
                <option value="{{ $ibu->id_kuda }}" {{ old('id_ibu') == $ibu->id_kuda ? 'selected' : '' }}>
                  {{ $ibu->nama_kuda }} - Betina
                </option>
              @endforeach
            </select>
          </div>
          @error('id_ibu')
            <small class="text-danger d-block mb-3">{{ $message }}</small>
          @enderror

          <label class="form-label">Ayah</label>
          <div class="input-group input-group-outline mb-1">
            <select name="id_ayah" class="form-control">
              <option value="">Tidak dipilih</option>
              @foreach($ayahOptions as $ayah)
                <option value="{{ $ayah->id_kuda }}" {{ old('id_ayah') == $ayah->id_kuda ? 'selected' : '' }}>
                  {{ $ayah->nama_kuda }} - Jantan
                </option>
              @endforeach
            </select>
          </div>
          @error('id_ayah')
            <small class="text-danger d-block mb-3">{{ $message }}</small>
          @enderror

          <hr class="horizontal dark my-3">

          {{-- SEKSI LISENSI --}}
          <div class="d-flex align-items-center gap-2 mb-2">
            <h6 class="mb-0">Lisensi Kuda</h6>
            <span class="badge bg-gradient-secondary">Opsional</span>
          </div>
          <p class="text-sm text-secondary mb-3">
            Kamu bisa langsung mengajukan lisensi saat menambah kuda. Lisensi akan menunggu persetujuan admin.
          </p>

          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="ajukanLisensi" name="ajukan_lisensi" value="1"
                   {{ old('ajukan_lisensi') ? 'checked' : '' }}
                   onchange="toggleLisensiForm(this)">
            <label class="form-check-label text-sm" for="ajukanLisensi">
              Sekaligus ajukan lisensi
            </label>
          </div>

          <div id="lisensiForm" style="{{ old('ajukan_lisensi') ? '' : 'display:none;' }}">

            <label class="form-label">Nomor Sertifikat <span class="text-danger">*</span></label>
            <div class="input-group input-group-outline mb-3">
              <input type="text"
                     name="nomor_sertifikat"
                     class="form-control"
                     placeholder="Contoh: LIS-KUDA-2026-001"
                     value="{{ old('nomor_sertifikat') }}">
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
                  <input type="date" name="tgl_terbit" class="form-control" value="{{ old('tgl_terbit') }}">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Masa Berlaku</label>
                <div class="input-group input-group-outline mb-3">
                  <input type="date" name="masa_berlaku" class="form-control" value="{{ old('masa_berlaku') }}">
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
                        placeholder="Deskripsi kondisi kesehatan kuda...">{{ old('riwayat_kesehatan') }}</textarea>
            </div>

          </div>
          {{-- END LISENSI --}}

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('kuda.index') }}" class="btn btn-light mb-0">Batal</a>
            <button type="submit" class="btn bg-gradient-dark mb-0">Simpan</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script>
function toggleLisensiForm(checkbox) {
    document.getElementById('lisensiForm').style.display = checkbox.checked ? '' : 'none';
}
</script>
@endsection
