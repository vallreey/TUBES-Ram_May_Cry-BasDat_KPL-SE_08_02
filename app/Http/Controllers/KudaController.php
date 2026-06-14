<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersKudaQuery;
use App\Models\Kuda;
use App\Models\Peternakan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KudaController extends Controller
{
    use FiltersKudaQuery;

    public function index(Request $request)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil data kuda berdasarkan role user lalu menerapkan search dan filter
        $kuda = $this->getKudaByRole($user, $request);

        // Menentukan halaman aktif
        $page = 'owned';

        // Menampilkan halaman data kuda
        return view('admin.kuda.index', compact('kuda', 'page'));
    }

    public function create()
    {

        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mencegah pembeli menambahkan kuda
        if ($user->role === User::ROLE_PEMBELI) {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa menambahkan kuda karena tidak memiliki peternakan.');
        }

        // Mengambil peternakan milik user agar pilihan ayah/ibu tidak mengambil kuda peternak lain
        $peternakan = Peternakan::where('id_user', $user->id_user)->first();

        if (!$peternakan) {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Anda belum memiliki peternakan.');
        }

        $ayahOptions = $this->getKudaIndukOptions($peternakan->id_peternakan, Kuda::GENDER_JANTAN);
        $ibuOptions = $this->getKudaIndukOptions($peternakan->id_peternakan, Kuda::GENDER_BETINA);

        // Menampilkan form tambah kuda
        return view('admin.kuda.create', compact('ayahOptions', 'ibuOptions'));
    }

    public function store(Request $request)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mencegah pembeli menyimpan data kuda
        if ($user->role === User::ROLE_PEMBELI) {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa menambahkan kuda.');
        }

        // Mengambil peternakan milik user
        $peternakan = Peternakan::where('id_user', $user->id_user)->first();

        // Mencegah user tanpa peternakan menambahkan kuda
        if (!$peternakan) {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Anda belum memiliki peternakan.');
        }

        // Memvalidasi data kuda sebelum disimpan.
        // id_ibu dan id_ayah harus berasal dari peternakan sendiri sesuai gendernya.
        $validated = $this->validateKudaData($request, $peternakan->id_peternakan);

        // Menyimpan data kuda baru
        Kuda::create([
            'nama_kuda'     => $validated['nama_kuda'],
            'jenis_kuda'    => $validated['jenis_kuda'],
            'gender'        => $validated['gender'],
            'status_jual'   => $validated['status_jual'],
            'harga_buka'    => $validated['harga_buka'],
            'id_peternakan' => $peternakan->id_peternakan,
            'id_ibu'        => $validated['id_ibu'] ?? null,
            'id_ayah'       => $validated['id_ayah'] ?? null,
        ]);

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

        // Default false agar tidak undefined di blade
        $bolehEditNama = false;

        // Dropdown induk hanya mengambil kuda dari peternakan yang sama.
        // Kuda yang sedang diedit tidak dimunculkan agar tidak bisa menjadi induknya sendiri.
        $ayahOptions = $this->getKudaIndukOptions(
            $kuda->id_peternakan,
            Kuda::GENDER_JANTAN,
            $kuda->id_kuda
        );

        $ibuOptions = $this->getKudaIndukOptions(
            $kuda->id_peternakan,
            Kuda::GENDER_BETINA,
            $kuda->id_kuda
        );

        // Admin bisa mengedit semua data kuda
        if ($user->role === User::ROLE_ADMIN) {
            return view('admin.kuda.edit', compact('kuda', 'bolehEditNama', 'ayahOptions', 'ibuOptions'));
        }

        // Peternak hanya bisa mengedit kuda miliknya sendiri
        if ($user->role === User::ROLE_PETERNAK) {
            if (!$this->canPeternakManageKuda($user, $kuda)) {
                return redirect()
                    ->route('kuda.index')
                    ->with('error', 'Anda tidak bisa mengedit kuda ini.');
            }

            return view('admin.kuda.edit', compact('kuda', 'bolehEditNama', 'ayahOptions', 'ibuOptions'));
        }

        // Pembeli hanya bisa mengedit nama kuda jika memenuhi aturan lisensi
        if ($user->role === User::ROLE_PEMBELI) {
            $bolehEditNama = $this->canPembeliEditNamaKuda($user, $kuda);

            if (!$bolehEditNama) {
                return redirect()
                    ->route('kuda.index')
                    ->with('error', 'Anda tidak memiliki lisensi untuk mengubah nama kuda ini.');
            }

            return view('admin.kuda.edit', compact('kuda', 'bolehEditNama', 'ayahOptions', 'ibuOptions'));
        }

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
        if ($user->role === User::ROLE_ADMIN) {
            $validated = $this->validateKudaData($request, $kuda->id_peternakan, $kuda->id_kuda);

            $kuda->update($validated);

            return redirect()
                ->route('kuda.index')
                ->with('success', 'Data kuda berhasil diperbarui.');
        }

        // Peternak hanya bisa memperbarui kuda miliknya sendiri
        if ($user->role === User::ROLE_PETERNAK) {
            if (!$this->canPeternakManageKuda($user, $kuda)) {
                return redirect()
                    ->route('kuda.index')
                    ->with('error', 'Anda tidak bisa mengubah data kuda ini.');
            }

            $validated = $this->validateKudaData($request, $kuda->id_peternakan, $kuda->id_kuda);

            $kuda->update($validated);

            return redirect()
                ->route('kuda.index')
                ->with('success', 'Data kuda berhasil diperbarui.');
        }

        // Pembeli hanya bisa memperbarui nama kuda
        if ($user->role === User::ROLE_PEMBELI) {
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
        if ($user->role === User::ROLE_PEMBELI) {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa menghapus data kuda.');
        }

        // Peternak hanya bisa menghapus kuda miliknya sendiri
        if ($user->role === User::ROLE_PETERNAK && !$this->canPeternakManageKuda($user, $kuda)) {
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

    private function getKudaByRole($user, Request $request)
    {
        // Query dasar untuk mengambil data kuda beserta relasinya
        $query = Kuda::with(['peternakan', 'lisensi', 'transaksi']);

        // Admin dapat melihat semua data kuda
        if ($user->role === User::ROLE_ADMIN) {
            return $this->applyKudaSearchAndFilters($query, $request)->get();
        }

        // Peternak hanya melihat kuda dari peternakannya sendiri
        if ($user->role === User::ROLE_PETERNAK) {
            $query->whereHas('peternakan', function ($q) use ($user) {
                $q->where('id_user', $user->id_user);
            });

            return $this->applyKudaSearchAndFilters($query, $request)->get();
        }

        // Pembeli hanya melihat kuda yang sudah dibeli
        if ($user->role === User::ROLE_PEMBELI) {
            $query->whereHas('transaksi', function ($q) use ($user) {
                $q->where('id_pembeli', $user->id_user)
                  ->where('status_transaksi', Transaksi::STATUS_SELESAI);
            });

            return $this->applyKudaSearchAndFilters($query, $request)->get();
        }

        // Mengembalikan data kosong jika role tidak dikenali
        return collect([]);
    }

    private function validateKudaData(Request $request, ?int $idPeternakan = null, ?int $exceptIdKuda = null)
    {
        // Memvalidasi input data kuda.
        // id_ibu dan id_ayah tidak cukup hanya exists, tetapi harus sesuai peternakan dan gender.
        $idIbuRules = [
            'nullable',
            Rule::exists('kuda', 'id_kuda')->where(function ($query) use ($idPeternakan) {
                return $query
                    ->where('id_peternakan', $idPeternakan)
                    ->where('gender', Kuda::GENDER_BETINA);
            }),
        ];

        $idAyahRules = [
            'nullable',
            Rule::exists('kuda', 'id_kuda')->where(function ($query) use ($idPeternakan) {
                return $query
                    ->where('id_peternakan', $idPeternakan)
                    ->where('gender', Kuda::GENDER_JANTAN);
            }),
        ];

        if ($exceptIdKuda) {
            $idIbuRules[] = Rule::notIn([$exceptIdKuda]);
            $idAyahRules[] = Rule::notIn([$exceptIdKuda]);
        }

        return $request->validate([
            'nama_kuda'   => 'required|string|max:100',
            'jenis_kuda'  => 'required|string|max:50',
            'gender'      => 'required|in:jantan,betina',
            'status_jual' => 'required|in:tersedia,terjual,breeding',
            'harga_buka'  => 'required|numeric|min:0',
            'id_ibu'      => $idIbuRules,
            'id_ayah'     => $idAyahRules,
        ], [
            'id_ibu.exists'  => 'Ibu harus kuda betina dari peternakan sendiri.',
            'id_ayah.exists' => 'Ayah harus kuda jantan dari peternakan sendiri.',
            'id_ibu.not_in'  => 'Kuda tidak bisa menjadi ibu untuk dirinya sendiri.',
            'id_ayah.not_in' => 'Kuda tidak bisa menjadi ayah untuk dirinya sendiri.',
        ]);
    }

    private function getKudaIndukOptions(int $idPeternakan, string $gender, ?int $exceptIdKuda = null)
    {
        $query = Kuda::where('id_peternakan', $idPeternakan)
            ->where('gender', $gender)
            ->orderBy('nama_kuda');

        if ($exceptIdKuda) {
            $query->where('id_kuda', '!=', $exceptIdKuda);
        }

        return $query->get();
    }

    private function canPeternakManageKuda($user, $kuda)
    {
        // Mengecek apakah peternak boleh mengelola kuda
        return $kuda->peternakan
            && $kuda->peternakan->id_user === $user->id_user
            && $kuda->status_jual !== Kuda::STATUS_TERJUAL;
    }

    private function canPembeliEditNamaKuda($user, $kuda)
    {
        // Mencari transaksi selesai milik pembeli
        $transaksi = Transaksi::where('id_kuda', $kuda->id_kuda)
            ->where('id_pembeli', $user->id_user)
            ->where('status_transaksi', Transaksi::STATUS_SELESAI)
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
