<!-- LIBRARY LOCAL untuk sidebar -->

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
      aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand px-4 py-3 m-0" href="{{ route('dashboard') }}">
      <img src="{{ asset('material/img/logo-ct-dark.png') }}" class="navbar-brand-img" width="26" height="26" alt="logo">
      <span class="ms-1 text-sm text-dark font-weight-bold">Ram May Cry</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0 mb-2">

  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      {{-- Dashboard --}}
        <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
            href="{{ route('dashboard') }}">

            <i class="material-symbols-rounded {{ request()->routeIs('dashboard') ? 'text-white' : 'text-dark' }}">
            dashboard
            </i>

            <span class="nav-link-text ms-1">Dashboard</span>
        </a>
        </li>

      {{-- Kuda --}}
        <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kuda.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
            href="{{ route('kuda.index') }}">

            <img
            src="{{ asset(
                request()->routeIs('kuda.*')
                ? 'material/img/sendiri/horseshoe_putih.png'
                : 'material/img/sendiri/horseshoe_hitam.png'
            ) }}"
            style="width:15px; height:15px;"
            >

            <span class="nav-link-text ms-1">Data Kuda</span>
        </a>
        </li>

      {{-- Peternakan --}}
        <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('peternakan.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
            href="{{ route('peternakan.index') }}">

            <i class="material-symbols-rounded {{ request()->routeIs('peternakan.*') ? 'text-white' : 'text-dark' }}">
            home
            </i>

            <span class="nav-link-text ms-1">Peternakan</span>
        </a>
        </li>

      {{-- Transaksi --}}
        <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('transaksi.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
            href="{{ route('transaksi.index') }}">

            <i class="material-symbols-rounded {{ request()->routeIs('transaksi.*') ? 'text-white' : 'text-dark' }}">
            receipt_long
            </i>

            <span class="nav-link-text ms-1">Transaksi</span>
        </a>
        </li>

      {{-- Kawin Silang --}}
    <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('kawin-silang.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
        href="{{ route('kawin-silang.index') }}">

        <img
        src="{{ asset(
            request()->routeIs('kawin-silang.*')
            ? 'material/img/sendiri/Gender_putih.png'
            : 'material/img/sendiri/Gender_hitam.png'
        ) }}"
        style="width:15px; height:15px;"
        >

        <span class="nav-link-text ms-1">Kawin Silang</span>
    </a>
    </li>

      {{-- Lisensi --}}
        <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('lisensi.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
            href="{{ route('lisensi.index') }}">

            <i class="material-symbols-rounded {{ request()->routeIs('lisensi.*') ? 'text-white' : 'text-dark' }}">
            verified
            </i>

            <span class="nav-link-text ms-1">Lisensi</span>
        </a>
        </li>

      <hr class="horizontal dark my-2">

      {{-- Hanya tampil untuk admin --}}
      @auth
        @if(auth()->user()->role === 'admin')
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
              href="{{ route('users.index') }}">
              <i class="material-symbols-rounded opacity-5">manage_accounts</i>
              <span class="nav-link-text ms-1">Kelola User</span>
            </a>
          </li>
        @endif
      @endauth

    </ul>
  </div>

  {{-- Profil User di bawah sidebar --}}
  <div class="sidenav-footer position-absolute w-100 bottom-0">
    <div class="mx-3">
      @auth
        <a class="btn btn-outline-dark mt-4 w-100" href="{{ route('profile') }}">
          <i class="material-symbols-rounded me-2">account_circle</i>
          {{ auth()->user()->nama_lengkap }}
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-outline-danger mt-2 w-100">
            <i class="material-symbols-rounded me-2">logout</i> Logout
          </button>
        </form>
      @endauth
    </div>
  </div>
</aside>
