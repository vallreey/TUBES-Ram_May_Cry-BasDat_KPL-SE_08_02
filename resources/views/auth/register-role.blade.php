<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="{{ asset('material/img/favicon.png') }}">
  <title>Daftar | Ram May Cry</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="{{ asset('material/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('material/css/nucleo-svg.css') }}" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="{{ asset('material/css/material-dashboard.css') }}" rel="stylesheet" />
  <style>
    .role-card {
      cursor: pointer;
      border: 2px solid transparent;
      border-radius: 12px;
      transition: all 0.3s ease;
      text-decoration: none;
      display: block;
      padding: 1.75rem 1.25rem;
      background: #fff;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .role-card:hover {
      border-color: #344767;
      transform: translateY(-4px);
      box-shadow: 0 8px 28px rgba(52,71,103,0.18);
      text-decoration: none;
    }
    .role-card .icon-wrap {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      font-size: 1.75rem;
      position: relative;
      overflow: hidden;
    }
    .role-card .role-icon-logo {
      width: 46px;
      height: 46px;
      object-fit: contain;
      position: relative;
      z-index: 1;
    }
    .role-card .role-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: #344767;
      margin-bottom: 0.35rem;
    }
    .role-card .role-desc {
      font-size: 0.82rem;
      color: #7b809a;
      line-height: 1.5;
    }
    .role-card-row {
      gap: 1rem;
    }
  </style>
</head>

<body class="bg-gray-200">
  <main class="main-content mt-0">
    <div class="page-header align-items-start min-vh-100"
      style="background-image: url('https://images.unsplash.com/photo-1553284965-83fd3e82fa5a?auto=format&fit=crop&w=1950&q=80');">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-6 col-md-9 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">

              {{-- Header Card --}}
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Daftar Akun</h4>
                  <p class="text-white text-sm text-center mb-2">Pilih jenis akun yang ingin Anda buat</p>
                </div>
              </div>

              {{-- Body Card --}}
              <div class="card-body px-4 py-4">
                <p class="text-center text-sm text-muted mb-4">Anda ingin mendaftar sebagai?</p>

                <div class="d-flex role-card-row justify-content-center">

                  {{-- Pilihan Pembeli --}}
                  <div class="flex-fill">
                    <a href="{{ route('register', ['role' => 'pembeli']) }}" class="role-card text-center">
                      {{-- Icon role pembeli menggunakan logo lokal di depan lingkaran --}}
                      <div class="icon-wrap bg-gradient-info mx-auto">
                        <img src="{{ asset('material/img/sendiri/horseshoe_putih.png') }}" alt="Icon Pembeli" class="role-icon-logo">
                      </div>
                      <p class="role-title">Pembeli</p>
                      <p class="role-desc">Saya ingin mencari dan membeli kuda dari peternakan terpercaya.</p>
                    </a>
                  </div>

                  {{-- Pilihan Peternak --}}
                  <div class="flex-fill">
                    <a href="{{ route('register', ['role' => 'peternak']) }}" class="role-card text-center">
                      {{-- Icon role peternak menggunakan logo lokal di depan lingkaran --}}
                      <div class="icon-wrap bg-gradient-success mx-auto">
                        <img src="{{ asset('material/img/sendiri/horseshoe_putih.png') }}" alt="Icon Peternak" class="role-icon-logo">
                      </div>
                      <p class="role-title">Peternak</p>
                      <p class="role-desc">Saya memiliki peternakan dan ingin menjual kuda di platform ini.</p>
                    </a>
                  </div>

                </div>

                <p class="text-sm text-center mt-4 mb-0">
                  Sudah punya akun?
                  <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">Login</a>
                </p>
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
