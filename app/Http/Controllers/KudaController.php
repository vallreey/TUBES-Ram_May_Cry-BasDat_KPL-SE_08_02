<?php

namespace App\Http\Controllers;

use App\Models\Kuda;
use App\Models\Peternakan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaksi;

class KudaController extends Controller
{
    public function index()
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        if ($user->role === User::ROLE_ADMIN) {
            $kuda = Kuda::with(['peternakan', 'lisensi'])->latest()->get();
        } elseif ($user->role === User::ROLE_PETERNAK) {
            $kuda = Kuda::with(['peternakan', 'lisensi'])
                ->whereHas('peternakan', function ($q) use ($user) {
                    $q->where('id_user', $user->id_user);
                })
                ->latest()->get();
        } else {
            $kuda = Kuda::with(['peternakan', 'lisensi'])
                ->whereHas('transaksi', function ($q) use ($user) {
                    $q->where('id_pembeli', $user->id_user)
                      ->where('status_transaksi', Transaksi::STATUS_SELESAI);
                })
                ->latest()->get();
        }

        // Mengambil data kuda berdasarkan role user
        $kuda = $this->getKudaByRole($user);
        staging

        // Menentukan halaman aktif
        $page = 'owned';

        // Menampilkan halaman data kuda
        return view('admin.kuda.index', compact('kuda', 'page'));
    }

    public function tersedia()
    {
        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        $kuda = Kuda::with(['peternakan', 'lisensi'])
            ->where('status_jual', Kuda::STATUS_TERSEDIA)
            ->latest()->get();

        $page = 'tersedia';

        return view('admin.kuda.index', compact('kuda', 'page'));

        // Menampilkan kuda dengan status tersedia
        return $this->showKudaByStatus('tersedia');
        staging
    }

    public function terjual()
    {
        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        $kuda = Kuda::with(['peternakan', 'lisensi'])
            ->where('status_jual', Kuda::STATUS_TERJUAL)
            ->latest()->get();

        $page = 'terjual';
        
        return view('admin.kuda.index', compact('kuda', 'page'));

        // Menampilkan kuda dengan status terjual
        return $this->showKudaByStatus('terjual');
        staging
    }

    public function breeding()
    {
        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        $kuda = Kuda::with(['peternakan', 'lisensi'])
            ->where('status_jual', Kuda::STATUS_BREEDING)
            ->latest()->get();

        $page = 'breeding';

        return view('admin.kuda.index', compact('kuda', 'page'));

        // Menampilkan kuda dengan status breeding
        return $this->showKudaByStatus('breeding');
        staging
    }

    public function create()
    {
        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        if (auth()->user()->role === User::ROLE_PEMBELI) {
            return redirect()->route('kuda.index')

        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mencegah pembeli menambahkan kuda
        if ($user->role === 'pembeli') {
            return redirect()
                ->route('kuda.index')
        staging
                ->with('error', 'Pembeli tidak bisa menambahkan kuda karena tidak memiliki peternakan.');
        }

        // Menampilkan form tambah kuda
        return view('admin.kuda.create');
    }

    public function store(Request $request)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        if ($user->role === User::ROLE_PEMBELI) {
            return redirect()->route('kuda.index')

        // Mencegah pembeli menyimpan data kuda
        if ($user->role === 'pembeli') {
            return redirect()
                ->route('kuda.index')
        staging
                ->with('error', 'Pembeli tidak bisa menambahkan kuda.');
        }

        // Memvalidasi data kuda sebelum disimpan
        $validated = $this->validateKudaData($request);

        // Mengambil peternakan milik user
        $peternakan = Peternakan::where('id_user', $user->id_user)->first();

        // Mencegah user tanpa peternakan menambahkan kuda
        if (!$peternakan) {
            return redirect()->route('kuda.index')
                ->with('error', 'Anda belum memiliki peternakan.');
        }

        // Menyimpan data kuda baru
        Kuda::create([
            'nama_kuda'     => $validated['nama_kuda'],
            'jenis_kuda'    => $validated['jenis_kuda'],
            'status_jual'   => $validated['status_jual'],
            'harga_buka'    => $validated['harga_buka'],
            'id_peternakan' => $peternakan->id_peternakan,
            'id_ibu'        => $validated['id_ibu'] ?? null,
            'id_ayah'       => $validated['id_ayah'] ?? null,
        ]);

     Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        return redirect()->route('kuda.index')->with('success', 'Data kuda berhasil ditambahkan.');
    }

    public function edit(Kuda $kuda)
    {
    $user = auth()->user();

        // Mengembalikan user ke halaman data kuda
        return redirect()
            ->route('kuda.index')
            ->with('success', 'Data kuda berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();
        staging

        // Mengambil data kuda beserta relasinya
        $kuda = Kuda::with(['peternakan', 'lisensi'])->findOrFail($id);

    Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
    // ADMIN boleh
    if ($user->role === User::ROLE_ADMIN) {
        return view('admin.kuda.edit', compact('kuda'));
    }

    // Cari transaksi selesai milik pembeli
    $transaksi = \App\Models\Transaksi::where('id_kuda', $kuda->id_kuda)
            ->where('id_pembeli', $user->id_user)
            ->where('status_transaksi', Transaksi::STATUS_SELESAI)
            ->latest()->first();

    $bolehEditNama = $user->role === User::ROLE_PEMBELI 
        && $transaksi 
        && (!$kuda->lisensi 
        || $transaksi->id_lisensi !== null);

    // Kalau pembeli tidak punya lisensi
    if ($user->role === User::ROLE_PEMBELI && !$bolehEditNama) {
        return redirect()->route('kuda.index')
            ->with('error', 'Anda tidak memiliki lisensi untuk mengubah nama kuda ini.');
    }

    return view('admin.kuda.edit', compact('kuda', 'bolehEditNama'));
    

        // Admin bisa mengedit semua data kuda
        if ($user->role === 'admin') {
            return view('admin.kuda.edit', compact('kuda'));
        }

        // Peternak hanya bisa mengedit kuda miliknya sendiri
        if ($user->role === 'peternak') {
            if (!$this->canPeternakManageKuda($user, $kuda)) {
                return redirect()
                    ->route('kuda.index')
                    ->with('error', 'Anda tidak bisa mengedit kuda ini.');
            }

            return view('admin.kuda.edit', compact('kuda'));
        }

        // Pembeli hanya bisa mengedit nama kuda jika memenuhi aturan lisensi
        if ($user->role === 'pembeli') {
            $bolehEditNama = $this->canPembeliEditNamaKuda($user, $kuda);

            if (!$bolehEditNama) {
                return redirect()
                    ->route('kuda.index')
                    ->with('error', 'Anda tidak memiliki akses untuk mengubah nama kuda ini.');
            }

            return view('admin.kuda.edit', compact('kuda', 'bolehEditNama'));
        }

        // Menangani role yang tidak dikenali
        return redirect()
            ->route('kuda.index')
            ->with('error', 'Role tidak dikenali.');
        staging
    }

    public function update(Request $request, $id)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
    // $kuda = Kuda::with(['peternakan', 'lisensi'])->findOrFail($id);
    kuda->load(['peternakan', 'lisensi']);

    // ADMIN boleh update semua field
    if ($user->role === User::ROLE_ADMIN) {
        $kuda->update($request->all());
        return redirect()->route('kuda.index')->with('success', 'Data kuda berhasil diperbarui.');
    }

    // PETERNAK hanya boleh update kuda miliknya dan belum terjual
    if ($user->role === User::ROLE_PETERNAK) {
        if (!$kuda->peternakan || $kuda->peternakan->id_user !== $user->id_user || $kuda->status_jual === Kuda::STATUS_TERJUAL) {
            return redirect()->route('kuda.index')
                ->with('error', 'Anda tidak bisa mengubah data kuda ini.');
        }

        $kuda->update($request->all());
        return redirect()->route('kuda.index')->with('success', 'Data kuda berhasil diperbarui.');
    }

    // PEMBELI hanya boleh ubah nama kuda jika memenuhi aturan lisensi
    if ($user->role === User::ROLE_PEMBELI) {
        $transaksi = \App\Models\Transaksi::where('id_kuda', $kuda->id_kuda)
            ->where('id_pembeli', $user->id_user)
            ->where('status_transaksi', Transaksi::STATUS_SELESAI)
            ->latest()->first();

        $bolehEditNama = $transaksi && (!$kuda->lisensi || $transaksi->id_lisensi !== null);

        // Mengambil data kuda yang akan diperbarui
        $kuda = Kuda::with(['peternakan', 'lisensi'])->findOrFail($id);

        // Admin bisa memperbarui semua data kuda
        if ($user->role === 'admin') {
            $validated = $this->validateKudaData($request);

            $kuda->update($validated);

            return redirect()
                ->route('kuda.index')
                ->with('success', 'Data kuda berhasil diperbarui.');
        }

        // Peternak hanya bisa memperbarui kuda miliknya sendiri
        if ($user->role === 'peternak') {
            if (!$this->canPeternakManageKuda($user, $kuda)) {
                return redirect()
                    ->route('kuda.index')
                    ->with('error', 'Anda tidak bisa mengubah data kuda ini.');
            }

            $validated = $this->validateKudaData($request);

            $kuda->update($validated);
          staging

            return redirect()
                ->route('kuda.index')
                ->with('success', 'Data kuda berhasil diperbarui.');
        }

        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        $request->validate(['nama_kuda' => 'required|string|max:100']);
        $kuda->update(['nama_kuda' => $request->nama_kuda]);

        return redirect()->route('kuda.index')->with('success', 'Nama kuda berhasil diperbarui.');
    }

    return redirect()->route('kuda.index')->with('error', 'Role tidak dikenali.');

        // Pembeli hanya bisa memperbarui nama kuda
        if ($user->role === 'pembeli') {
            if (!$this->canPembeliEditNamaKuda($user, $kuda)) {
                return redirect()
                    ->route('kuda.index')
                    ->with('error', 'Anda tidak memiliki akses untuk mengubah nama kuda ini.');
            }

            // Memvalidasi nama kuda yang diubah pembeli
            $request->validate([
                'nama_kuda' => 'required|string|max:100',
            ]);

            // Pembeli hanya boleh mengubah nama kuda
            $kuda->update([
                'nama_kuda' => $request->nama_kuda,
            ]);

            return redirect()
                ->route('kuda.index')
                ->with('success', 'Nama kuda berhasil diperbarui.');
        }

        // Menangani role yang tidak dikenali
        return redirect()
            ->route('kuda.index')
            ->with('error', 'Role tidak dikenali.');
        staging
    }

    public function destroy($id)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();
        $kuda->load('peternakan');

        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        if ($user->role === User::ROLE_PEMBELI) {
            return redirect()->route('kuda.index')->with('error', 'Pembeli tidak bisa menghapus data kuda.');
        }

        if ($user->role === User::ROLE_PETERNAK && (!$kuda->peternakan || $kuda->peternakan->id_user !== $user->id_user || $kuda->status_jual === Kuda::STATUS_TERJUAL)) {
            return redirect()->route('kuda.index')->with('error', 'Anda tidak bisa menghapus kuda ini.');

        // Mengambil data kuda yang akan dihapus
        $kuda = Kuda::with('peternakan')->findOrFail($id);

        // Mencegah pembeli menghapus data kuda
        if ($user->role === 'pembeli') {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa menghapus data kuda.');
        }

        // Peternak hanya bisa menghapus kuda miliknya sendiri
        if ($user->role === 'peternak' && !$this->canPeternakManageKuda($user, $kuda)) {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Anda tidak bisa menghapus kuda ini.');
        staging
        }

        // Menghapus data kuda
        $kuda->delete();
        return redirect()->route('kuda.index')->with('success', 'Data kuda berhasil dihapus.');
    }

    private function getKudaByRole($user)
    {
        // Query dasar untuk mengambil data kuda beserta relasinya
        $query = Kuda::with(['peternakan', 'lisensi', 'transaksi'])->latest();

        // Admin dapat melihat semua data kuda
        if ($user->role === 'admin') {
            return $query->get();
        }

        // Peternak hanya melihat kuda dari peternakannya sendiri
        if ($user->role === 'peternak') {
            return $query
                ->whereHas('peternakan', function ($q) use ($user) {
                    $q->where('id_user', $user->id_user);
                })
                ->get();
        }

        // Pembeli hanya melihat kuda yang sudah dibeli
        if ($user->role === 'pembeli') {
            return $query
                ->whereHas('transaksi', function ($q) use ($user) {
                    $q->where('id_pembeli', $user->id_user)
                      ->where('status_transaksi', 'selesai');
                })
                ->get();
        }

        // Mengembalikan data kosong jika role tidak dikenali
        return collect([]);
    }

    private function showKudaByStatus($status)
    {
        // Mengambil data kuda berdasarkan status jual
        $kuda = Kuda::with(['peternakan', 'lisensi', 'transaksi'])
            ->where('status_jual', $status)
            ->latest()
            ->get();

        // Menentukan halaman aktif sesuai status
        $page = $status;

        // Menampilkan halaman data kuda
        return view('admin.kuda.index', compact('kuda', 'page'));
    }

    private function validateKudaData(Request $request)
    {
        // Memvalidasi input data kuda
        return $request->validate([
            'nama_kuda'   => 'required|string|max:100',
            'jenis_kuda'  => 'required|string|max:50',
            'status_jual' => 'required|in:tersedia,terjual,breeding',
            'harga_buka'  => 'required|numeric|min:0',
            'id_ibu'      => 'nullable|exists:kuda,id_kuda',
            'id_ayah'     => 'nullable|exists:kuda,id_kuda',
        ]);
    }

    private function canPeternakManageKuda($user, $kuda)
    {
        // Mengecek apakah peternak boleh mengelola kuda
        return $kuda->peternakan
            && $kuda->peternakan->id_user === $user->id_user
            && $kuda->status_jual !== 'terjual';
    }

    private function canPembeliEditNamaKuda($user, $kuda)
    {
        // Mencari transaksi selesai milik pembeli
        $transaksi = Transaksi::where('id_kuda', $kuda->id_kuda)
            ->where('id_pembeli', $user->id_user)
            ->where('status_transaksi', 'selesai')
            ->latest()
            ->first();

        // Jika tidak ada transaksi selesai, nama kuda tidak bisa diubah
        if (!$transaksi) {
            return false;
        }

        // Nama bisa diubah jika kuda tidak berlisensi atau transaksi membeli lisensi
        return !$kuda->lisensi || $transaksi->id_lisensi !== null;
    }
}
