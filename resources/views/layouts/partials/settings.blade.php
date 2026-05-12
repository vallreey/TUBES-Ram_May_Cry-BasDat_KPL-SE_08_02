<div class="fixed-plugin">
  <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
    <i class="material-symbols-rounded py-2">settings</i>
  </a>
  <div class="card shadow-lg">
    <div class="card-header pb-0 pt-3">
      <div class="float-start">
        <h5 class="mt-3 mb-0">Konfigurasi Tampilan</h5>
        <p>Pilih tema sidebar.</p>
      </div>
      <div class="float-end mt-4">
        <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
          <i class="material-symbols-rounded">clear</i>
        </button>
      </div>
    </div>
    <hr class="horizontal dark my-1">
    <div class="card-body pt-sm-3 pt-0">
      <div>
        <h6 class="mb-0">Warna Sidebar</h6>
      </div>
      <a href="javascript:void(0)" class="switch-trigger background-color">
        <div class="badge-colors my-2 text-start">
          <span class="badge filter bg-gradient-primary" data-color="primary" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-dark active" data-color="dark" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
        </div>
      </a>
      <div class="mt-3">
        <h6 class="mb-0">Tipe Sidenav</h6>
        <p class="text-sm">Pilih tampilan sidenav.</p>
      </div>
      <div class="d-flex">
        <button class="btn bg-gradient-dark px-3 mb-2" data-class="bg-gradient-dark" onclick="sidebarType(this)">Dark</button>
        <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparan</button>
        <button class="btn bg-gradient-dark px-3 mb-2 active ms-2" data-class="bg-white" onclick="sidebarType(this)">Putih</button>
      </div>
      <p class="text-sm d-xl-none d-block mt-2">Kamu dapat mengubah visibilitas sidenav di halaman responsif dari menu di pojok kanan atas.</p>
      <div class="mt-3">
        <h6 class="mb-0">Navbar Fixed</h6>
      </div>
      <div class="form-check form-switch ps-0">
        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
      </div>
    </div>
  </div>
</div>
