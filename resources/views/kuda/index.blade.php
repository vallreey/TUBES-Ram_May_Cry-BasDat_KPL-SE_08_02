@extends('layouts.material')

@section('title', 'Data Kuda')
@section('breadcrumb', 'Data Kuda')

@section('content')
<div class="row mt-4">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between">
          <h6>Data Kuda</h6>
          <a href="{{ route('kuda.create') }}" class="btn bg-gradient-dark mb-0">Tambah Kuda</a> <!-- Tombol untuk tambah data -->
        </div>
        <p class="text-sm mb-0">Data kuda terdaftar dalam sistem</p>
      </div>
      <div class="card-body px-0 pb-2">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Kuda</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jenis Kuda</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Jual</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Harga Buka</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
              </tr>
            </thead>
            <tbody>
                @foreach($kuda as $item)
                <tr>
                    <td style="padding-left: 20px;"> <!-- Menambahkan margin kiri -->
                    {{ $item->nama_kuda }}
                    </td>
                    <td>{{ $item->jenis_kuda }}</td>
                    <td class="text-center">{{ $item->status_jual }}</td>
                    <td class="text-center">Rp {{ number_format($item->harga_buka, 0, ',', '.') }}</td>
                    <td class="text-center"> <!-- Men-center-kan tombol -->
                    <a href="{{ route('kuda.edit', $item->id_kuda) }}" class="btn btn-light mb-0">Edit</a>
                    <form action="{{ route('kuda.destroy', $item->id_kuda) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn bg-gradient-dark mb-0">Hapus</button>
                    </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection