@extends('layouts.material')

@section('title', 'Pilih Pengajuan Kawin Silang')
@section('breadcrumb', 'Kawin Silang')

@section('content')

<div class="row mt-4 justify-content-center">
    <div class="col-lg-10">

        <div class="card">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-dark shadow-dark border-radius-lg py-4">
                    <h4 class="text-white font-weight-bolder text-center mb-1">
                        Pengajuan Kawin Silang
                    </h4>
                    <p class="text-white text-sm text-center mb-0">
                        Pilih peran kuda yang ingin Anda ajukan
                    </p>
                </div>
            </div>

            <div class="card-body py-5">

                <p class="text-center text-secondary mb-4">
                    Kuda Anda ingin diajukan sebagai?
                </p>

                <div class="row justify-content-center">

                    <div class="col-md-5 mb-4">
                        <a href="{{ route('kawin-silang.create', ['sebagai' => 'betina']) }}"
                           class="text-decoration-none">
                            <div class="card shadow-sm h-100 pilihan-breeding-card">
                                <div class="card-body text-center py-5">

                                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center"
                                         style="width:80px; height:80px; border-radius:50%; background:#e91e63;">
                                        <img src="{{ asset('material/img/sendiri/Gender_putih.png') }}"
                                             style="width:38px; height:38px;">
                                    </div>

                                    <h4 class="text-dark mb-3">
                                        Kuda Betina
                                    </h4>

                                    <p class="text-secondary mb-0">
                                        Kuda yang dipilih akan menjadi calon ibu.
                                    </p>

                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-5 mb-4">
                        <a href="{{ route('kawin-silang.create', ['sebagai' => 'jantan']) }}"
                           class="text-decoration-none">
                            <div class="card shadow-sm h-100 pilihan-breeding-card">
                                <div class="card-body text-center py-5">

                                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center"
                                         style="width:80px; height:80px; border-radius:50%; background:#348ceb;">
                                        <img src="{{ asset('material/img/sendiri/Gender_putih.png') }}"
                                             style="width:38px; height:38px;">
                                    </div>

                                    <h4 class="text-dark mb-3">
                                        Kuda Jantan
                                    </h4>

                                    <p class="text-secondary mb-0">
                                        Kuda yang dipilih akan menjadi calon ayah.
                                    </p>

                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('kawin-silang.index') }}" class="btn btn-light">
                        Kembali
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
    .pilihan-breeding-card {
        transition: all 0.2s ease;
        border-radius: 18px;
    }

    .pilihan-breeding-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
    }
</style>

@endsection
