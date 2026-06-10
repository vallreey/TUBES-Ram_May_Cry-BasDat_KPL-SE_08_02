<?php

namespace App\Http\Controllers;

use App\Models\KawinSilang;
use App\Models\Kuda;
use App\Models\Peternakan;
use Illuminate\Http\Request;

class KawinSilangController extends Controller
{
    public function index()
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil data kawin silang berdasarkan role user
        $breeding = $this->getBreedingByRole($user);

        // Menampilkan halaman kawin silang
        return view('admin.kawin-silang.index', compact('breeding'));
    }

    public function create(Request $request)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil pilihan peran dari query string
        $sebagai = $request->query('sebagai');

        // Menampilkan halaman pilih peran jika belum memilih jantan atau betina
        if (!in_array($sebagai, ['jantan', 'betina'])) {
            return view('admin.kawin-silang.pilih-genderkuda');
        }

        // Mengambil kuda milik user
        $kudaSaya = $this->getKudaMilikUser($user);

        // Mengambil peternakan tujuan beserta kuda tersedia
        $peternakan = $this->getPeternakanTujuan($user);

        // Menampilkan form pengajuan kawin silang
        return view('admin.kawin-silang.create', compact(
            'kudaSaya',
            'peternakan',
            'sebagai'
        ));
    }

    public function store(Request $request)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Memvalidasi input pengajuan kawin silang
        $validated = $this->validateBreedingData($request);

        // Mengambil data kuda milik user
        $kudaSaya = Kuda::with('peternakan')->findOrFail($validated['id_kuda_saya']);

        // Mengambil data kuda tujuan
        $kudaTujuan = Kuda::with('peternakan')->findOrFail($validated['id_kuda_tujuan']);

        // Mencegah user mengajukan kuda yang bukan miliknya
        if (!$this->isKudaMilikUser($user, $kudaSaya)) {
            return redirect()
                ->back()
                ->with('error', 'Kuda yang diajukan bukan milik Anda.');
        }

        // Mencegah memilih kuda yang sama
        if ($kudaSaya->id_kuda === $kudaTujuan->id_kuda) {
            return redirect()
                ->back()
                ->with('error', 'Kuda tujuan tidak boleh sama dengan kuda sendiri.');
        }

        // Mencegah memilih kuda yang tidak tersedia
        if (!$this->isKudaTersediaUntukPengajuan($kudaSaya, $kudaTujuan)) {
            return redirect()
                ->back()
                ->with('error', 'Kuda yang dipilih tidak tersedia untuk kawin silang.');
        }

        // Menentukan posisi kuda sebagai betina atau jantan
        $pasangan = $this->setPasanganBreeding(
            $validated['pengajuan_sebagai'],
            $user,
            $kudaSaya,
            $kudaTujuan
        );

        // Menyimpan pengajuan dengan status pending
        KawinSilang::create([
            'tgl_pengajuan'       => now(),
            'tgl_breeding'        => $validated['tgl_breeding'] ?? null,
            'status_hasil'        => 'pending',
            'perkiraan_kelahiran' => null,
            'id_pengaju'          => $user->id_user,
            'pengajuan_sebagai'   => $validated['pengajuan_sebagai'],
            'id_pemilik_betina'   => $pasangan['id_pemilik_betina'],
            'id_pemilik_jantan'   => $pasangan['id_pemilik_jantan'],
            'id_betina'           => $pasangan['id_betina'],
            'id_jantan'           => $pasangan['id_jantan'],
        ]);

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Pengajuan kawin silang berhasil dikirim dan menunggu persetujuan.');
    }

    public function update(Request $request, $id)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil data kawin silang
        $breeding = KawinSilang::with(['kudaBetina', 'kudaJantan'])->findOrFail($id);

        // Memvalidasi aksi kawin silang
        $validated = $request->validate([
            'aksi' => 'required|in:acc,tolak,berhasil,gagal',
            'perkiraan_kelahiran' => 'nullable|date',
        ]);

        // Menentukan pihak yang harus menyetujui pengajuan
        $idPihakTerkait = $this->getIdPihakTerkait($breeding);

        // Mencegah user lain memproses ACC atau Tolak
        if (
            in_array($validated['aksi'], ['acc', 'tolak'])
            && $user->role !== 'admin'
            && $user->id_user !== $idPihakTerkait
        ) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki akses untuk memproses pengajuan ini.');
        }

        // Mencegah pengajuan pending diproses ulang
        if (
            in_array($validated['aksi'], ['acc', 'tolak'])
            && $breeding->status_hasil !== 'pending'
        ) {
            return redirect()
                ->back()
                ->with('error', 'Pengajuan ini sudah diproses.');
        }

        // Menyetujui pengajuan dan mengubah kuda menjadi breeding
        if ($validated['aksi'] === 'acc') {
            $breeding->update([
                'status_hasil' => 'proses',
                'perkiraan_kelahiran' => $validated['perkiraan_kelahiran'] ?? null,
            ]);

            $this->updateStatusKudaBreeding(
                $breeding->kudaBetina,
                $breeding->kudaJantan,
                'breeding'
            );
        }

        // Menolak pengajuan
        if ($validated['aksi'] === 'tolak') {
            $breeding->update([
                'status_hasil' => 'ditolak',
            ]);
        }

        // Menandai proses kawin silang berhasil
        if ($validated['aksi'] === 'berhasil') {
            $breeding->update([
                'status_hasil' => 'berhasil',
                'perkiraan_kelahiran' => $validated['perkiraan_kelahiran'] ?? $breeding->perkiraan_kelahiran,
            ]);

            $this->updateStatusKudaBreeding(
                $breeding->kudaBetina,
                $breeding->kudaJantan,
                'tersedia'
            );
        }

        // Menandai proses kawin silang gagal
        if ($validated['aksi'] === 'gagal') {
            $breeding->update([
                'status_hasil' => 'gagal',
            ]);

            $this->updateStatusKudaBreeding(
                $breeding->kudaBetina,
                $breeding->kudaJantan,
                'tersedia'
            );
        }

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Status kawin silang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Mengambil data kawin silang
        $breeding = KawinSilang::with(['kudaBetina', 'kudaJantan'])->findOrFail($id);

        // Hanya admin yang bisa menghapus data kawin silang
        if (auth()->user()->role !== 'admin') {
            return redirect()
                ->route('kawin-silang.index')
                ->with('error', 'Hanya admin yang bisa menghapus data kawin silang.');
        }

        // Mengembalikan status kuda menjadi tersedia
        $this->updateStatusKudaBreeding(
            $breeding->kudaBetina,
            $breeding->kudaJantan,
            'tersedia'
        );

        // Menghapus data kawin silang
        $breeding->delete();

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Data kawin silang berhasil dihapus.');
    }

    private function getBreedingByRole($user)
    {
        // Query dasar data kawin silang beserta relasinya
        $query = KawinSilang::with([
            'kudaBetina',
            'kudaJantan',
            'pemilikBetina',
            'pemilikJantan',
            'pengaju'
        ])->latest();

        // Admin dapat melihat semua data kawin silang
        if ($user->role === 'admin') {
            return $query->get();
        }

        // User hanya melihat data kawin silang yang melibatkan dirinya
        return $query
            ->where(function ($q) use ($user) {
                $q->where('id_pemilik_betina', $user->id_user)
                  ->orWhere('id_pemilik_jantan', $user->id_user)
                  ->orWhere('id_pengaju', $user->id_user);
            })
            ->get();
    }

    private function getKudaMilikUser($user)
    {
        // Peternak mengambil kuda dari peternakannya sendiri
        if ($user->role === 'peternak') {
            return Kuda::with('peternakan')
                ->where('status_jual', 'tersedia')
                ->whereHas('peternakan', function ($q) use ($user) {
                    $q->where('id_user', $user->id_user);
                })
                ->get();
        }

        // Pembeli mengambil kuda dari transaksi yang sudah selesai
        if ($user->role === 'pembeli') {
            return Kuda::with('peternakan')
                ->where('status_jual', 'tersedia')
                ->whereHas('transaksi', function ($q) use ($user) {
                    $q->where('id_pembeli', $user->id_user)
                      ->where('status_transaksi', 'selesai');
                })
                ->get();
        }

        // Admin tidak memiliki kuda pribadi untuk diajukan
        return collect([]);
    }

    private function getPeternakanTujuan($user)
    {
        // Mengambil peternakan tujuan beserta kuda yang tersedia
        return Peternakan::with(['user', 'kuda' => function ($q) {
                $q->where('status_jual', 'tersedia');
            }])
            ->when($user->role === 'peternak', function ($q) use ($user) {
                $q->where('id_user', '!=', $user->id_user);
            })
            ->latest()
            ->get();
    }

    private function validateBreedingData(Request $request)
    {
        // Memvalidasi data pengajuan kawin silang
        return $request->validate([
            'pengajuan_sebagai' => 'required|in:jantan,betina',
            'id_kuda_saya'      => 'required|exists:kuda,id_kuda',
            'id_kuda_tujuan'    => 'required|exists:kuda,id_kuda',
            'tgl_breeding'      => 'nullable|date',
        ]);
    }

    private function isKudaMilikUser($user, $kuda)
    {
        // Peternak dicek dari kepemilikan peternakan
        if ($user->role === 'peternak') {
            return $kuda->peternakan
                && $kuda->peternakan->id_user === $user->id_user;
        }

        // Pembeli dicek dari transaksi selesai
        if ($user->role === 'pembeli') {
            return $kuda->transaksi()
                ->where('id_pembeli', $user->id_user)
                ->where('status_transaksi', 'selesai')
                ->exists();
        }

        return false;
    }

    private function isKudaTersediaUntukPengajuan($kudaSaya, $kudaTujuan)
    {
        // Mengecek kedua kuda masih tersedia untuk diajukan
        return $kudaSaya->status_jual === 'tersedia'
            && $kudaTujuan->status_jual === 'tersedia';
    }

    private function setPasanganBreeding($sebagai, $user, $kudaSaya, $kudaTujuan)
    {
        // Menentukan pasangan jika kuda saya sebagai betina
        if ($sebagai === 'betina') {
            return [
                'id_betina'         => $kudaSaya->id_kuda,
                'id_jantan'         => $kudaTujuan->id_kuda,
                'id_pemilik_betina' => $user->id_user,
                'id_pemilik_jantan' => $kudaTujuan->peternakan->id_user,
            ];
        }

        // Menentukan pasangan jika kuda saya sebagai jantan
        return [
            'id_jantan'         => $kudaSaya->id_kuda,
            'id_betina'         => $kudaTujuan->id_kuda,
            'id_pemilik_jantan' => $user->id_user,
            'id_pemilik_betina' => $kudaTujuan->peternakan->id_user,
        ];
    }

    private function getIdPihakTerkait($breeding)
    {
        // Menentukan pihak yang harus menyetujui pengajuan
        return $breeding->pengajuan_sebagai === 'betina'
            ? $breeding->id_pemilik_jantan
            : $breeding->id_pemilik_betina;
    }

    private function updateStatusKudaBreeding($kudaPertama, $kudaKedua, $status)
    {
        // Mengubah status kuda pertama
        if ($kudaPertama) {
            $kudaPertama->update(['status_jual' => $status]);
        }

        // Mengubah status kuda kedua
        if ($kudaKedua) {
            $kudaKedua->update(['status_jual' => $status]);
        }
    }
}
