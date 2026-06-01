<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="{{ asset('material/img/favicon.png') }}">
  <title>Daftar Peternak | Ram May Cry</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="{{ asset('material/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('material/css/nucleo-svg.css') }}" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="{{ asset('material/css/material-dashboard.css') }}" rel="stylesheet" />
  <style>
    .section-label {
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.07em;
      color: #7b809a;
      margin: 1rem 0 0.5rem;
    }
    .divider-label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin: 1rem 0 0.75rem;
    }
    .divider-label hr {
      flex: 1;
      border-color: #e9ecef;
      margin: 0;
    }
  </style>
</head>

<body class="bg-gray-200">
  <main class="main-content mt-0">
    <div class="page-header align-items-start min-vh-100"
      style="background-image: url('https://images.unsplash.com/photo-1553284965-83fd3e82fa5a?auto=format&fit=crop&w=1950&q=80');">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container my-auto py-4">
        <div class="row">
          <div class="col-lg-6 col-md-9 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">

              {{-- Header Card --}}
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">
                    <i class="fa fa-horse me-2"></i> Daftar sebagai Peternak
                  </h4>
                  <p class="text-white text-sm text-center mb-2">Sistem Informasi Jual Beli Kuda</p>
                </div>
              </div>

              {{-- Body Card --}}
              <div class="card-body px-4">

                @if($errors->any())
                  <div class="alert alert-danger text-white text-sm">
                    {{ $errors->first() }}
                  </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}">
                  @csrf
                  {{-- Role tersembunyi --}}
                  <input type="hidden" name="role" value="peternak">

                  {{-- ===== Data Akun ===== --}}
                  <div class="divider-label">
                    <hr>
                    <span class="section-label">Data Akun</span>
                    <hr>
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <input
                        type="text"
                        name="nama_lengkap"
                        class="form-control"
                        placeholder="Nama Lengkap"
                        value="{{ old('nama_lengkap') }}"
                        required
                    >
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <input type="email"
                        name="email"
                        class="form-control"
                        placeholder="Email"
                        value="{{ old('email') }}"
                        required>
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <input type="text"
                        name="no_telp"
                        class="form-control"
                        placeholder="No. Telepon"
                        value="{{ old('no_telp') }}">
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <input type="text"
                        name="alamat"
                        class="form-control"
                        placeholder="Alamat Pribadi"
                        value="{{ old('alamat') }}">
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <input type="password"
                        name="password"
                        class="form-control"
                        placeholder="Password"
                        required>
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <input type="password"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Konfirmasi Password"
                        required>
                  </div>

                  {{-- ===== Data Peternakan ===== --}}
                  <div class="divider-label">
                    <hr>
                    <span class="section-label">Data Peternakan</span>
                    <hr>
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <input
                        type="text"
                        name="nama_peternakan"
                        class="form-control"
                        placeholder="Nama Peternakan"
                        value="{{ old('nama_peternakan') }}"
                        required
                    >
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <input
                        type="number"
                        name="kapasitas_kandang"
                        class="form-control"
                        placeholder="Kapasitas Kandang (jumlah kuda)"
                        value="{{ old('kapasitas_kandang', 0) }}"
                        min="0"
                        required
                    >
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <input
                        type="text"
                        name="lokasi_map"
                        class="form-control"
                        placeholder="Lokasi / Kota (contoh: Purwokerto)"
                        value="{{ old('lokasi_map') }}"
                    >
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <textarea
                        name="alamat_lengkap"
                        class="form-control"
                        placeholder="Alamat Lengkap Peternakan"
                        rows="2"
                        style="padding-top: 0.75rem;"
                    >{{ old('alamat_lengkap') }}</textarea>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn bg-gradient-success w-100 mt-3 mb-2">Daftar sebagai Peternak</button>
                  </div>

                  <p class="text-sm text-center mt-2">
                    Ingin daftar sebagai pembeli?
                    <a href="{{ route('register', ['role' => 'pembeli']) }}" class="text-primary text-gradient font-weight-bold">Klik di sini</a>
                  </p>

                  <p class="text-sm text-center mt-1">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">Login</a>
                  </p>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>

      <footer class="footer position-absolute bottom-2 py-2 w-100">
        <div class="container">
          <div class="row">
            <div class="col-12 text-center">
              <p class="text-white text-sm mb-0">&copy; {{ date('Y') }} Ram May Cry</p>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>

  <script src="{{ asset('material/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('material/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('material/js/material-dashboard.min.js') }}"></script>
</body>
</html>
