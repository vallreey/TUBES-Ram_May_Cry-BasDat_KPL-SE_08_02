@extends('layouts.material')

@section('title', 'Ajukan Kawin Silang')
@section('breadcrumb', 'Ajukan Kawin Silang')

@section('content')

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">

            <div class="card-header pb-0">
                <h6>Ajukan Kawin Silang</h6>

                <p class="text-sm mb-0">
                    Pengajuan sebagai:
                    <span class="badge badge-sm bg-gradient-dark">
                        {{ ucfirst($sebagai) }}
                    </span>
                </p>
            </div>

            <div class="card-body">

                <form action="{{ route('kawin-silang.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="pengajuan_sebagai" value="{{ $sebagai }}">

                    <div class="alert alert-info text-white text-sm">
                        @if($sebagai === 'betina')
                            Kuda yang Anda pilih akan menjadi calon ibu.
                            Kuda tujuan akan dianggap sebagai calon ayah.
                        @else
                            Kuda yang Anda pilih akan menjadi calon ayah.
                            Kuda tujuan akan dianggap sebagai calon ibu.
                        @endif
                    </div>

                    <label class="form-label">Kuda Saya</label>
                    <div class="input-group input-group-outline mb-3">
                        <select name="id_kuda_saya" class="form-control" required>
                            <option value="">Pilih Kuda Saya</option>

                            @foreach($kudaSaya as $k)
                                <option value="{{ $k->id_kuda }}">
                                    {{ $k->nama_kuda }}
                                    -
                                    {{ $k->peternakan->nama_peternakan ?? 'Kuda Milik Pembeli' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label class="form-label">Peternakan Tujuan</label>
                    <div class="input-group input-group-outline mb-3">
                        <select id="peternakan_tujuan" class="form-control" required>
                            <option value="">Pilih Peternakan</option>

                            @foreach($peternakan as $p)
                                <option value="{{ $p->id_peternakan }}">
                                    {{ $p->nama_peternakan }}
                                    -
                                    Pemilik: {{ $p->user->nama_lengkap ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label class="form-label">
                        @if($sebagai === 'betina')
                            Pilih Kuda Jantan / Calon Ayah dari Peternakan Tujuan
                        @else
                            Pilih Kuda Betina / Calon Ibu dari Peternakan Tujuan
                        @endif
                    </label>

                    <div class="input-group input-group-outline mb-3">
                        <select name="id_kuda_tujuan" id="kuda_tujuan" class="form-control" required>
                            <option value="">Pilih peternakan terlebih dahulu</option>

                            @foreach($peternakan as $p)
                                @foreach($p->kuda as $k)
                                    <option value="{{ $k->id_kuda }}"
                                            data-peternakan="{{ $p->id_peternakan }}">
                                        {{ $k->nama_kuda }}
                                        -
                                        @if($sebagai === 'betina')
                                            Calon Ayah
                                        @else
                                            Calon Ibu
                                        @endif
                                        -
                                        {{ $p->nama_peternakan }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <label class="form-label">Tanggal Breeding</label>
                    <div class="input-group input-group-outline mb-3">
                        <input type="date"
                               name="tgl_breeding"
                               class="form-control"
                               value="{{ old('tgl_breeding') }}">
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('kawin-silang.create') }}" class="btn btn-light mb-0">
                            Ganti Peran
                        </a>

                        <button type="submit" class="btn bg-gradient-dark mb-0">
                            Kirim Pengajuan
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>

<script>
    const peternakanSelect = document.getElementById('peternakan_tujuan');
    const kudaSelect = document.getElementById('kuda_tujuan');
    const allKudaOptions = Array.from(kudaSelect.querySelectorAll('option'));

    peternakanSelect.addEventListener('change', function () {
        const selectedPeternakan = this.value;

        kudaSelect.innerHTML = '<option value="">Pilih Kuda Tujuan</option>';

        allKudaOptions.forEach(option => {
            if (option.dataset.peternakan === selectedPeternakan) {
                kudaSelect.appendChild(option.cloneNode(true));
            }
        });
    });
</script>

@endsection
