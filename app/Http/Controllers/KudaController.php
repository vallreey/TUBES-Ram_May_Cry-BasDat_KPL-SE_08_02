<?php

namespace App\Http\Controllers;

use App\Models\Kuda;
use App\Models\Peternakan;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class KudaController extends Controller
{
    public function index()
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil data kuda berdasarkan role user
        $kuda = $this->getKudaByRole($user);

        // Menentukan halaman aktif
        $page = 'owned';

        // Menampilkan halaman data kuda
        return view('admin.kuda.index', compact('kuda', 'page'));
    }

    public function tersedia()
    {
        // Menampilkan kuda dengan status tersedia
        return $this->showKudaByStatus('tersedia');
    }

    public function terjual()
    {
        // Menampilkan kuda dengan status terjual
        return $this->showKudaByStatus('terjual');
    }

    public function breeding()
    {
        // Menampilkan kuda dengan status breeding
        return $this->showKudaByStatus('breeding');
    }

    public function create()
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mencegah pembeli menambahkan kuda
        if ($user->role === 'pembeli') {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa menambahkan kuda karena tidak memiliki peternakan.');
        }

        // Menampilkan form tambah kuda
        return view('admin.kuda.create');
    }

    public function store(Request $request)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mencegah pembeli menyimpan data kuda
        if ($user->role === 'pembeli') {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa menambahkan kuda.');
        }

        // Memvalidasi data kuda sebelum disimpan
        $validated = $this->validateKudaData($request);

        // Mengambil peternakan milik user
        $peternakan = Peternakan::where('id_user', $user->id_user)->first();

        // Mencegah user tanpa peternakan menambahkan kuda
        if (!$peternakan) {
            return redirect()
                ->route('kuda.index')
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

        // Mengembalikan user ke halaman data kuda
        return redirect()
            ->route('kuda.index')
            ->with('success', 'Data kuda berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil data kuda beserta relasinya
        $kuda = Kuda::with(['peternakan', 'lisensi'])->findOrFail($id);

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
    }

    public function update(Request $request, $id)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

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

            return redirect()
                ->route('kuda.index')
                ->with('success', 'Data kuda berhasil diperbarui.');
        }

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
    }

    public function destroy($id)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

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
        }

        // Menghapus data kuda
        $kuda->delete();

        return redirect()
            ->route('kuda.index')
            ->with('success', 'Data kuda berhasil dihapus.');
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
