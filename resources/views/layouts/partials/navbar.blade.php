<nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
  <div class="container-fluid py-1 px-3">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
          <a class="opacity-5 text-dark" href="{{ route('dashboard') }}">Halaman</a>
        </li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
          @yield('breadcrumb', 'Dashboard')
        </li>
      </ol>
      <h6 class="font-weight-bolder mb-0">@yield('breadcrumb', 'Dashboard')</h6>
    </nav>

    {{-- Kanan: notifikasi & profil --}}
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
      <ul class="navbar-nav d-flex align-items-center justify-content-end ms-md-auto">

        {{-- Toggle sidebar mobile --}}
        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
          <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
            <div class="sidenav-toggler-inner">
              <i class="sidenav-toggler-line"></i>
              <i class="sidenav-toggler-line"></i>
              <i class="sidenav-toggler-line"></i>
            </div>
          </a>
        </li>

        {{-- Settings --}}
        <li class="nav-item px-3 d-flex align-items-center">
          <a href="javascript:;" class="nav-link text-body p-0">
            <i class="material-symbols-rounded fixed-plugin-button-nav">settings</i>
          </a>
        </li>

        {{-- Profil --}}
        <li class="nav-item d-flex align-items-center">
          @auth
            <a href="{{ route('profile') }}" class="nav-link text-body font-weight-bold px-0">
              <i class="material-symbols-rounded">account_circle</i>
            </a>
          @else
            <a href="{{ route('login') }}" class="nav-link text-body font-weight-bold px-0">
              <i class="material-symbols-rounded">login</i>
            </a>
          @endauth
        </li>

      </ul>
    </div>

  </div>
</nav>
