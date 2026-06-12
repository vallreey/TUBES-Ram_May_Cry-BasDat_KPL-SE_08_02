<?php

namespace App\Http\Controllers;

use App\Models\KawinSilang;
use App\Models\Kuda;
use App\Models\Lisensi;
use App\Models\PenawaranBreeding;
use App\Models\Peternakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KawinSilangController extends Controller
{
    public function index()
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil data kawin silang berdasarkan role
        $breeding = $this->getBreedingByRole($user);

        // Menampilkan halaman kawin silang
        return view('admin.kawin-silang.index', compact('breeding'));
    }

    public function create(Request $request)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil pilihan peran pengajuan
        $sebagai = $request->query('sebagai');

        // Menampilkan halaman pilih peran jika belum memilih
        if (!in_array($sebagai, ['jantan', 'betina'])) {
            return view('admin.kawin-silang.pilih-genderkuda');
        }

        // Mengambil kuda milik user
        $kudaSaya = $this->getKudaMilikUser($user);

        // Mengambil peternakan tujuan
        $peternakan = $this->getPeternakanTujuan($user);

        // Menampilkan form pengajuan
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

        // Memvalidasi data pengajuan
        $validated = $this->validateBreedingData($request);

        // Mengambil data kuda milik user
        $kudaSaya = Kuda::with('peternakan')->findOrFail($validated['id_kuda_saya']);

        // Mengambil data kuda tujuan
        $kudaTujuan = Kuda::with('peternakan')->findOrFail($validated['id_kuda_tujuan']);

        // Mengecek kepemilikan kuda user
        if (!$this->isKudaMilikUser($user, $kudaSaya)) {
            return back()->with('error', 'Kuda yang diajukan bukan milik Anda.');
        }

        // Mencegah memilih kuda yang sama
        if ($kudaSaya->id_kuda === $kudaTujuan->id_kuda) {
            return back()->with('error', 'Kuda tujuan tidak boleh sama dengan kuda sendiri.');
        }

        // Mengecek kuda tersedia untuk pengajuan
        if (!$this->isKudaTersediaUntukPengajuan($kudaSaya, $kudaTujuan)) {
            return back()->with('error', 'Kuda yang dipilih tidak tersedia untuk kawin silang.');
        }

        // Menentukan pasangan betina dan jantan
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
            'id_anak'             => null,
        ]);

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Pengajuan kawin silang berhasil dikirim dan menunggu penawaran.');
    }

    public function update(Request $request, $id)
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil data kawin silang
        $breeding = KawinSilang::with([
            'kudaBetina',
            'kudaJantan',
            'anak',
            'penawaran',
        ])->findOrFail($id);

        // Memvalidasi aksi
        $request->validate([
            'aksi' => 'required|in:kirim_penawaran,terima_penawaran,tolak_penawaran,nego,terima_nego,tolak_nego,selesai_breeding,berhasil,gagal,konfirmasi_anak',
        ]);

        // Memproses aksi berdasarkan request
        return match ($request->aksi) {
            'kirim_penawaran'  => $this->kirimPenawaran($request, $breeding, $user),
            'terima_penawaran' => $this->terimaPenawaran($breeding, $user),
            'tolak_penawaran'  => $this->tolakPenawaran($breeding, $user),
            'nego'             => $this->negoPenawaran($request, $breeding, $user),
            'terima_nego'      => $this->terimaNego($breeding, $user),
            'tolak_nego'       => $this->tolakNego($breeding, $user),
            'selesai_breeding' => $this->selesaiBreeding($request, $breeding, $user),
            'berhasil'         => $this->selesaiBreeding($request, $breeding, $user),
            'gagal'            => $this->tandaiGagal($breeding, $user),
            'konfirmasi_anak'  => $this->konfirmasiAnak($breeding, $user),
        };
    }

    public function destroy($id)
    {
        // Mengambil data kawin silang
        $breeding = KawinSilang::with(['kudaBetina', 'kudaJantan'])->findOrFail($id);

        // Hanya admin yang bisa menghapus
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Hanya admin yang bisa menghapus data kawin silang.');
        }

        // Mengembalikan status induk jika masih proses
        if ($breeding->status_hasil === 'proses') {
            $this->updateStatusKudaBreeding(
                $breeding->kudaBetina,
                $breeding->kudaJantan,
                'tersedia'
            );
        }

        // Menghapus data kawin silang
        $breeding->delete();

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Data kawin silang berhasil dihapus.');
    }

    private function kirimPenawaran(Request $request, $breeding, $user)
    {
        // Mengecek pihak penerima pengajuan
        if (!$this->isPenerimaPengajuan($user, $breeding)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengirim penawaran.');
        }

        // Penawaran hanya bisa dikirim saat pending
        if ($breeding->status_hasil !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah tidak bisa diberi penawaran.');
        }

        // Memvalidasi data penawaran
        $validated = $request->validate([
            'harga_ditawarkan' => 'required|numeric|min:0',
            'pakai_lisensi'    => 'required|in:0,1',
            'catatan'          => 'nullable|string',
        ]);

        // Menyimpan penawaran
        PenawaranBreeding::updateOrCreate(
            ['id_breeding' => $breeding->id_breeding],
            [
                'id_penawar'          => $user->id_user,
                'id_penerima_tawaran' => $breeding->id_pengaju,
                'harga_ditawarkan'    => $validated['harga_ditawarkan'],
                'harga_nego'          => null,
                'pakai_lisensi'       => $validated['pakai_lisensi'],
                'status_penawaran'    => 'ditawarkan',
                'catatan'             => $validated['catatan'] ?? null,
            ]
        );

        // Mengubah status menjadi penawaran
        $breeding->update([
            'status_hasil' => 'penawaran',
        ]);

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Penawaran berhasil dikirim ke pengaju.');
    }

    private function terimaPenawaran($breeding, $user)
    {
        // Hanya pengaju yang bisa menerima
        if (!$this->isPengaju($user, $breeding)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menerima penawaran.');
        }

        // Mengecek penawaran tersedia
        if (!$breeding->penawaran || $breeding->penawaran->status_penawaran !== 'ditawarkan') {
            return back()->with('error', 'Penawaran tidak tersedia.');
        }

        DB::transaction(function () use ($breeding) {
            // Menyetujui penawaran
            $breeding->penawaran->update([
                'status_penawaran' => 'disetujui',
            ]);

            // Mengubah status kawin silang menjadi proses
            $breeding->update([
                'status_hasil' => 'proses',
            ]);

            // Mengubah kedua induk menjadi breeding
            $this->updateStatusKudaBreeding(
                $breeding->kudaBetina,
                $breeding->kudaJantan,
                'breeding'
            );
        });

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Penawaran diterima. Kawin silang masuk tahap proses.');
    }

    private function tolakPenawaran($breeding, $user)
    {
        // Hanya pengaju yang bisa menolak
        if (!$this->isPengaju($user, $breeding)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menolak penawaran.');
        }

        // Mengecek penawaran tersedia
        if (!$breeding->penawaran) {
            return back()->with('error', 'Penawaran tidak ditemukan.');
        }

        // Menolak penawaran
        $breeding->penawaran->update([
            'status_penawaran' => 'ditolak',
        ]);

        // Mengubah status pengajuan menjadi ditolak
        $breeding->update([
            'status_hasil' => 'ditolak',
        ]);

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Penawaran berhasil ditolak.');
    }

    private function negoPenawaran(Request $request, $breeding, $user)
    {
        // Hanya pengaju yang bisa nego
        if (!$this->isPengaju($user, $breeding)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk negosiasi.');
        }

        // Mengecek status penawaran
        if (!$breeding->penawaran || $breeding->penawaran->status_penawaran !== 'ditawarkan') {
            return back()->with('error', 'Penawaran tidak tersedia untuk dinegosiasikan.');
        }

        // Memvalidasi harga nego
        $validated = $request->validate([
            'harga_nego' => 'required|numeric|min:0',
            'catatan'    => 'nullable|string',
        ]);

        // Menyimpan harga nego
        $breeding->penawaran->update([
            'harga_nego'       => $validated['harga_nego'],
            'status_penawaran' => 'nego',
            'catatan'          => $validated['catatan'] ?? $breeding->penawaran->catatan,
        ]);

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Negosiasi harga berhasil dikirim.');
    }

    private function terimaNego($breeding, $user)
    {
        // Hanya penerima pengajuan yang bisa menerima nego
        if (!$this->isPenerimaPengajuan($user, $breeding)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menerima negosiasi.');
        }

        // Mengecek status negosiasi
        if (!$breeding->penawaran || $breeding->penawaran->status_penawaran !== 'nego') {
            return back()->with('error', 'Data negosiasi tidak tersedia.');
        }

        DB::transaction(function () use ($breeding) {
            // Menyetujui negosiasi
            $breeding->penawaran->update([
                'status_penawaran' => 'disetujui',
            ]);

            // Mengubah status kawin silang menjadi proses
            $breeding->update([
                'status_hasil' => 'proses',
            ]);

            // Mengubah status kedua induk menjadi breeding
            $this->updateStatusKudaBreeding(
                $breeding->kudaBetina,
                $breeding->kudaJantan,
                'breeding'
            );
        });

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Negosiasi diterima. Kawin silang masuk tahap proses.');
    }

    private function tolakNego($breeding, $user)
    {
        // Hanya penerima pengajuan yang bisa menolak nego
        if (!$this->isPenerimaPengajuan($user, $breeding)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menolak negosiasi.');
        }

        // Mengecek status negosiasi
        if (!$breeding->penawaran || $breeding->penawaran->status_penawaran !== 'nego') {
            return back()->with('error', 'Data negosiasi tidak tersedia.');
        }

        // Menolak negosiasi
        $breeding->penawaran->update([
            'status_penawaran' => 'ditolak',
        ]);

        // Mengubah status pengajuan menjadi ditolak
        $breeding->update([
            'status_hasil' => 'ditolak',
        ]);

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Negosiasi ditolak.');
    }

    private function selesaiBreeding(Request $request, $breeding, $user)
    {
        // Mengecek akses kelola hasil breeding
        if (!$this->canKelolaHasilBreeding($user, $breeding)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah hasil breeding.');
        }

        // Hanya status proses yang bisa diselesaikan
        if ($breeding->status_hasil !== 'proses') {
            return back()->with('error', 'Breeding belum berada dalam status proses.');
        }

        // Memvalidasi data anak
        $validated = $request->validate([
            'nama_anak'           => 'nullable|string|max:60',
            'jenis_kuda'          => 'nullable|string|max:50',
            'perkiraan_kelahiran' => 'nullable|date',
        ]);

        DB::transaction(function () use ($breeding, $validated) {
            // Membuat anak dalam status hold
            $anak = $breeding->anak ?? $this->buatAnakHasilBreeding($breeding, $validated);

            // Membuat lisensi otomatis dari peternakan penerima
            $this->buatLisensiAnak($breeding, $anak);

            // Mengubah status menjadi menunggu konfirmasi anak
            $breeding->update([
                'status_hasil'        => 'menunggu_konfirmasi_anak',
                'perkiraan_kelahiran' => $validated['perkiraan_kelahiran'] ?? $breeding->perkiraan_kelahiran,
                'id_anak'             => $anak->id_kuda,
            ]);

            // Mengembalikan status induk menjadi tersedia
            $this->updateStatusKudaBreeding(
                $breeding->kudaBetina,
                $breeding->kudaJantan,
                'tersedia'
            );
        });

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Breeding selesai. Anak kuda menunggu konfirmasi dari pengaju.');
    }

    private function tandaiGagal($breeding, $user)
    {
        // Mengecek akses kelola hasil breeding
        if (!$this->canKelolaHasilBreeding($user, $breeding)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah hasil breeding.');
        }

        // Hanya status proses yang bisa ditandai gagal
        if ($breeding->status_hasil !== 'proses') {
            return back()->with('error', 'Breeding belum berada dalam status proses.');
        }

        DB::transaction(function () use ($breeding) {
            // Mengubah status menjadi gagal
            $breeding->update([
                'status_hasil' => 'gagal',
            ]);

            // Mengembalikan status induk menjadi tersedia
            $this->updateStatusKudaBreeding(
                $breeding->kudaBetina,
                $breeding->kudaJantan,
                'tersedia'
            );
        });

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Breeding ditandai gagal.');
    }

    private function konfirmasiAnak($breeding, $user)
    {
        // Hanya pengaju yang bisa menerima anak
        if (!$this->isPengaju($user, $breeding)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menerima anak kuda.');
        }

        // Anak hanya bisa dikonfirmasi saat menunggu konfirmasi
        if ($breeding->status_hasil !== 'menunggu_konfirmasi_anak') {
            return back()->with('error', 'Anak kuda belum siap untuk dikonfirmasi.');
        }

        // Mengecek data anak
        if (!$breeding->anak) {
            return back()->with('error', 'Data anak kuda tidak ditemukan.');
        }

        // Mengambil peternakan milik pengaju
        $peternakanPengaju = $this->getPeternakanPengaju($breeding);

        DB::transaction(function () use ($breeding, $peternakanPengaju) {
            // Memindahkan anak ke peternakan pengaju
            $breeding->anak->update([
                'id_peternakan' => $peternakanPengaju->id_peternakan,
                'status_jual'   => 'tersedia',
            ]);

            // Mengubah status kawin silang menjadi berhasil
            $breeding->update([
                'status_hasil' => 'berhasil',
            ]);
        });

        return redirect()
            ->route('kawin-silang.index')
            ->with('success', 'Anak kuda berhasil diterima dan masuk ke data peternakan Anda.');
    }

    private function getBreedingByRole($user)
    {
        // Query dasar data kawin silang
        $query = KawinSilang::with([
            'kudaBetina',
            'kudaJantan',
            'anak',
            'pemilikBetina',
            'pemilikJantan',
            'pengaju',
            'penawaran',
            'penawaran.penawar',
            'penawaran.penerimaTawaran',
        ])->latest();

        // Admin melihat semua data
        if ($user->role === 'admin') {
            return $query->get();
        }

        // User melihat data yang melibatkan dirinya
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
        // Peternak mengambil kuda aktif dari peternakannya
        if ($user->role === 'peternak') {
            return Kuda::with('peternakan')
                ->where('status_jual', 'tersedia')
                ->whereHas('peternakan', function ($q) use ($user) {
                    $q->where('id_user', $user->id_user);
                })
                ->get();
        }

        // Pembeli mengambil kuda dari transaksi selesai
        if ($user->role === 'pembeli') {
            return Kuda::with('peternakan')
                ->where('status_jual', 'tersedia')
                ->whereHas('transaksi', function ($q) use ($user) {
                    $q->where('id_pembeli', $user->id_user)
                      ->where('status_transaksi', 'selesai');
                })
                ->get();
        }

        return collect([]);
    }

    private function getPeternakanTujuan($user)
    {
        // Mengambil peternakan tujuan beserta kuda tersedia
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
        // Memvalidasi data pengajuan
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
        // Mengecek kedua kuda tersedia
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

    private function buatAnakHasilBreeding($breeding, $validated)
    {
        // Mengambil peternakan penerima pengajuan
        $peternakanPenerima = $this->getPeternakanPenerima($breeding);

        // Mengambil harga hasil penawaran awal
        $hargaAnak = $this->getHargaPenawaranFinal($breeding);

        // Membuat anak kuda dalam status hold
        return Kuda::create([
            'nama_kuda'     => $validated['nama_anak'] ?? 'Anak Breeding #' . $breeding->id_breeding,
            'jenis_kuda'    => $validated['jenis_kuda'] ?? 'Hasil Kawin Silang',
            'status_jual'   => 'hold',
            'harga_buka'    => $hargaAnak,
            'id_peternakan' => $peternakanPenerima->id_peternakan,
            'id_ibu'        => $breeding->id_betina,
            'id_ayah'       => $breeding->id_jantan,
        ]);
    }

    private function buatLisensiAnak($breeding, $anak)
    {
        // Mengecek lisensi anak sudah ada
        if ($anak->lisensi()->exists()) {
            return;
        }

        // Mengambil peternakan penerima pengajuan
        $peternakanPenerima = $this->getPeternakanPenerima($breeding);

        // Membuat lisensi otomatis
        Lisensi::create([
            'nomor_sertifikat'  => $this->generateNomorSertifikat($breeding, $anak),
            'penerbit'          => $peternakanPenerima->nama_peternakan,
            'tgl_terbit'        => now(),
            'masa_berlaku'      => now()->addYears(5),
            'keaslian_ras'      => 'Hasil Kawin Silang',
            'riwayat_kesehatan' => 'Anak hasil kawin silang dari sistem OreNoAiba.',
            'id_kuda'           => $anak->id_kuda,
        ]);
    }

    private function generateNomorSertifikat($breeding, $anak)
    {
        // Membuat nomor sertifikat otomatis
        return 'BRD-' . str_pad($breeding->id_breeding, 4, '0', STR_PAD_LEFT)
            . '-' . str_pad($anak->id_kuda, 4, '0', STR_PAD_LEFT);
    }

    private function getHargaPenawaranFinal($breeding)
    {
        // Mengambil harga final dari penawaran atau nego
        if (!$breeding->penawaran) {
            return 0;
        }

        return $breeding->penawaran->harga_nego
            ?? $breeding->penawaran->harga_ditawarkan
            ?? 0;
    }

    private function getPeternakanPenerima($breeding)
    {
        // Mengambil id user penerima pengajuan
        $idPenerima = $this->getIdPihakTerkait($breeding);

        // Mengambil peternakan milik penerima
        $peternakan = Peternakan::where('id_user', $idPenerima)->first();

        // Menghentikan proses jika peternakan tidak ditemukan
        if (!$peternakan) {
            abort(422, 'Peternakan penerima pengajuan tidak ditemukan.');
        }

        return $peternakan;
    }

    private function getPeternakanPengaju($breeding)
    {
        // Mengambil peternakan milik pengaju
        $peternakan = Peternakan::where('id_user', $breeding->id_pengaju)->first();

        // Menghentikan proses jika peternakan tidak ditemukan
        if (!$peternakan) {
            abort(422, 'Peternakan pengaju tidak ditemukan.');
        }

        return $peternakan;
    }

    private function getIdPihakTerkait($breeding)
    {
        // Menentukan pihak penerima pengajuan
        return $breeding->pengajuan_sebagai === 'betina'
            ? $breeding->id_pemilik_jantan
            : $breeding->id_pemilik_betina;
    }

    private function isPenerimaPengajuan($user, $breeding)
    {
        // Mengecek user adalah penerima pengajuan
        return $user->id_user === $this->getIdPihakTerkait($breeding);
    }

    private function isPengaju($user, $breeding)
    {
        // Mengecek user adalah pengaju
        return $user->id_user === $breeding->id_pengaju;
    }

    private function canKelolaHasilBreeding($user, $breeding)
    {
        // Mengecek user boleh mengelola hasil breeding
        return $user->role === 'admin'
            || $this->isPenerimaPengajuan($user, $breeding);
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
