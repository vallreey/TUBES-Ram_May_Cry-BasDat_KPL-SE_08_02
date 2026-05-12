<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('material/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('material/img/favicon.png') }}">
  <title>@yield('title', 'Ram May Cry') | Sistem Informasi Kuda</title>

  {{-- Fonts & Icons --}}
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="{{ asset('material/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('material/css/nucleo-svg.css') }}" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

  {{-- Material Dashboard CSS --}}
  <link id="pagestyle" href="{{ asset('material/css/material-dashboard.css') }}" rel="stylesheet" />

  <link rel="stylesheet" href="{{ asset('material/css/lib_sendiri/sidebar-animation.css') }}">

  <link rel="stylesheet" href="{{ asset('material/css/lib_sendiri/loading.css') }}">

  <link rel="stylesheet" href="{{ asset('material/css/lib_sendiri/loading.css') }}">

  @stack('styles')
</head>

<body class="g-sidenav-show bg-gray-100">
    <!-- LIBRARY LOCAL Loading Animation -->
    <div id="page-loader">

        <div class="loader-content">

            <img
                src="{{ asset('material/img/sendiri/ASMC_walk_l.gif') }}"
                alt="Loading"
                class="loader-gif"
            >

            <span class="loader-text">
                NOW LOADING...
            </span>

        </div>

    </div>


  {{-- Sidebar --}}
  @include('layouts.partials.sidebar')

  {{-- Main Content --}}
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

    {{-- Navbar --}}
    @include('layouts.partials.navbar')

    {{-- Page Content --}}
    <div class="container-fluid py-2">
      @yield('content')

      {{-- Footer --}}
      @include('layouts.partials.footer')
    </div>

  </main>

  {{-- Settings Plugin --}}
  @include('layouts.partials.settings')

  {{-- Core JS --}}
  <script src="{{ asset('material/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('material/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('material/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('material/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script src="{{ asset('material/js/material-dashboard.min.js') }}"></script>

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = { damping: '0.5' }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <!-- LIBRARY LOCAL Loading JS -->
    <script src="{{ asset('material/js/lib_sendiri/loading.js') }}">
    </script>

  @stack('scripts')
</body>

</html>
