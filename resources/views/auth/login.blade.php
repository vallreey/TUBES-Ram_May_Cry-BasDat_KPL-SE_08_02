<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="{{ asset('material/img/favicon.png') }}">
  <title>Login | Ram May Cry</title>

  <!-- Google Fonts Library -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Nucleo Icons Library -->
  <link href="{{ asset('material/css/nucleo-icons.css') }}" rel="stylesheet" />

  <!-- Nucleo SVG Icons Library -->
  <link href="{{ asset('material/css/nucleo-svg.css') }}" rel="stylesheet" />

  <!-- Font Awesome Icon Library -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

  <!-- Google Material Symbols Icon Library -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

  <!-- Material Dashboard CSS Library -->
  <link id="pagestyle" href="{{ asset('material/css/material-dashboard.css') }}" rel="stylesheet" />
</head>

<body class="bg-gray-200">
  <main class="main-content mt-0">
    <div class="page-header align-items-start min-vh-100"
      style="background-image: url('https://images.unsplash.com/photo-1553284965-83fd3e82fa5a?auto=format&fit=crop&w=1950&q=80');">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">

              {{-- Header Card --}}
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Login</h4>
                  <p class="text-white text-sm text-center mb-2">Sistem Informasi Jual Beli Kuda</p>
                </div>
              </div>

              {{-- Body Card --}}
              <div class="card-body">

                {{-- Tampilkan error validasi --}}
                @if($errors->any())
                  <div class="alert alert-danger text-white text-sm">
                    {{ $errors->first() }}
                  </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="text-start">
                  @csrf

                  <div class="input-group input-group-outline my-3">
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Email"
                    value="{{ old('email') }}"
                    required
                >
            </div>

                  <div class="input-group input-group-outline mb-3">
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Password"
                        required
                    >
                  </div>

                  <div class="form-check form-switch d-flex align-items-center mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label mb-0 ms-3" for="rememberMe">Ingat saya</label>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Login</button>
                  </div>

                  <p class="mt-2 text-sm text-center">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-primary text-gradient font-weight-bold">Daftar</a>
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

  <script src="{{ asset('material/js/core/popper.min.js') }}"></script> <!-- Library Bootstrap di public\material\js-->
  <script src="{{ asset('material/js/core/bootstrap.min.js') }}"></script> <!-- Library Bootstrap di public\material\js-->
  <script src="{{ asset('material/js/material-dashboard.min.js') }}"></script> <!-- Library Bootstrap di public\material\js-->
</body>
</html>
