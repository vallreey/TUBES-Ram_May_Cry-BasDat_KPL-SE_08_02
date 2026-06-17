@if($kuda->ayah || $kuda->ibu)
    <hr>

    <h6>Data Induk</h6>

    @if($kuda->ayah)
        <p class="text-sm mb-1">
            <strong>Ayah:</strong> {{ $kuda->ayah->nama_kuda }}
        </p>
    @endif

    @if($kuda->ibu)
        <p class="text-sm mb-3">
            <strong>Ibu:</strong> {{ $kuda->ibu->nama_kuda }}
        </p>
    @endif
@endif
