<?php

namespace App\Http\Controllers;

use App\Models\Kuda;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        // Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif

        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil data transaksi beserta relasinya berdasarkan role user
        $transaksi = $this->getTransaksiByRole($user);

        // Menampilkan halaman transaksi
        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        // Mengarahkan ke halaman transaksi karena form manual belum digunakan
        return redirect()
            ->route('transaksi.index')
            ->with('error', 'Transaksi dibuat melalui tombol beli kuda.');
    }

    public function store(Request $request)
    {
        // Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif

        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mencegah selain pembeli membuat transaksi pembelian
        if ($user->role !== User::ROLE_PEMBELI) {
            return back()->with('error', 'Hanya pembeli yang bisa membeli kuda.');
        }

        // Memvalidasi input pembelian
        $validated = $request->validate([
            'id_kuda'        => 'required|exists:kuda,id_kuda',
            'pakai_lisensi' => 'required|in:0,1',
        ]);

        // Mengambil data kuda beserta peternakan dan lisensinya
        $kuda = Kuda::with(['peternakan', 'lisensi'])
            ->findOrFail($validated['id_kuda']);

        // Mencegah pembelian kuda yang tidak tersedia
        if ($kuda->status_jual !== Kuda::STATUS_TERSEDIA) {
            return back()->with('error', 'Kuda ini sudah tidak tersedia.');
        }

        // Mencegah transaksi jika data peternakan kuda tidak tersedia
        if (!$kuda->peternakan) {
            return back()->with('error', 'Data peternakan kuda tidak ditemukan.');
        }

        // Menentukan apakah pembelian menggunakan lisensi
        $idLisensi = $this->getLisensiPembelian($validated['pakai_lisensi'], $kuda);

        // staging

        // Membuat transaksi dengan status awal pending (AUTOMATION)
        Transaksi::create([
            'status_transaksi' => Transaksi::STATUS_PENDING,
            'tgl_transaksi'    => now(),
            'harga_final'      => $kuda->harga_buka,
            'id_kuda'          => $kuda->id_kuda,
            'id_lisensi'       => $idLisensi,
            'id_pembeli'       => $user->id_user,
            'id_penjual'       => $kuda->peternakan->id_user,
        ]);

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Pengajuan pembelian berhasil dikirim ke penjual.');
    }

    public function show($id)
    {
        // Mengambil detail transaksi berdasarkan ID
        $transaksi = Transaksi::with(['kuda', 'pembeli', 'penjual', 'lisensi'])
            ->findOrFail($id);

        // Menampilkan halaman detail transaksi
        return view('admin.transaksi.show', compact('transaksi'));
    }

    public function edit($id)
    {
        // Mengarahkan ke halaman transaksi karena edit manual belum digunakan
        return redirect()
            ->route('transaksi.index')
            ->with('error', 'Transaksi hanya bisa diproses melalui View Detail.');
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        // Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif

        // Mengambil user yang sedang login
        $user = auth()->user();

        // Memvalidasi aksi transaksi
        $validated = $request->validate([
            'aksi' => 'required|in:terima,tolak',
        ]);

        // Mengambil transaksi beserta data kuda
        $transaksi->load('kuda');

        // Mencegah selain peternak pemilik transaksi memproses transaksi
        if (
            $user->role !== User::ROLE_PETERNAK ||
            $transaksi->id_penjual !== $user->id_user
        ) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki akses untuk transaksi ini.');
        }

        // Mencegah transaksi diproses lebih dari satu kali
        if ($transaksi->status_transaksi !== Transaksi::STATUS_PENDING) {
            return redirect()
                ->back()
                ->with('error', 'Transaksi ini sudah diproses.');
        }

        // Menerima transaksi dan mengubah status kuda menjadi terjual (AUTOMATION)
        if ($validated['aksi'] === 'terima') {
            $transaksi->update([
                'status_transaksi' => Transaksi::STATUS_SELESAI,
            ]);

            if ($transaksi->kuda) {
                $transaksi->kuda->update([
                    'status_jual' => Kuda::STATUS_TERJUAL,
                ]);
            }
        }

        // Menolak transaksi dan mengubah status transaksi menjadi dibatalkan (AUTOMATION)
        if ($validated['aksi'] === 'tolak') {
            $transaksi->update([
                'status_transaksi' => Transaksi::STATUS_DIBATALKAN,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Status transaksi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Mengarahkan ke halaman transaksi karena hapus transaksi belum digunakan
        return redirect()
            ->route('transaksi.index')
            ->with('error', 'Fitur hapus transaksi belum tersedia.');
    }

    private function getTransaksiByRole($user)
    {
        // Query dasar untuk mengambil transaksi beserta relasinya
        $query = Transaksi::with([
            'kuda',
            'pembeli',
            'penjual',
            'lisensi',
        ])->latest();

        // Admin dapat melihat semua transaksi
        if ($user->role === User::ROLE_ADMIN) {
            return $query->get();
        }

        // Pembeli hanya melihat transaksi miliknya sendiri
        if ($user->role === User::ROLE_PEMBELI) {
            return $query
                ->where('id_pembeli', $user->id_user)
                ->get();
        }

        // Peternak hanya melihat transaksi penjualan miliknya
        if ($user->role === User::ROLE_PETERNAK) {
            return $query
                ->where('id_penjual', $user->id_user)
                ->get();
        }

        // Mengembalikan data kosong jika role tidak dikenali
        return collect([]);
    }

    private function getLisensiPembelian($pakaiLisensi, $kuda)
    {
        // Mengembalikan null jika pembeli tidak memilih lisensi
        if ($pakaiLisensi == 0) {
            return null;
        }

        // Mengembalikan null jika kuda tidak memiliki lisensi
        if (!$kuda->lisensi) {
            return null;
        }

        // Mengembalikan id lisensi jika pembeli memilih lisensi
        return $kuda->lisensi->id_lisensi;
    }
}
