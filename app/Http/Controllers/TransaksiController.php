<?php

namespace App\Http\Controllers;

use App\Models\Kuda;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kuda;

class TransaksiController extends Controller
{
    public function index()
    {
        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
    $user = auth()->user();

    $query = Transaksi::with([
        'kuda',
        'pembeli',
        'penjual',
        'lisensi'
    ])->latest();

    // ADMIN bisa melihat semua transaksi
    if ($user->role === User::ROLE_ADMIN) {
        $transaksi = $query->get();
    }

    // PEMBELI hanya melihat transaksi miliknya sendiri
    elseif ($user->role === User::ROLE_PEMBELI) {
        $transaksi = $query->where('id_pembeli', $user->id_user)->get();
    }

    // PETERNAK hanya melihat transaksi yang dia lakukan sebagai penjual
    elseif ($user->role === User::ROLE_PETERNAK) {
            $transaksi = $query->where('id_penjual', $user->id_user)->get();
        }

    // Jika role tidak dikenal
    else {
        $transaksi = collect([]);
    }

    return view('admin.transaksi.index', compact('transaksi'));

        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil data transaksi beserta relasinya
        $transaksi = $this->getTransaksiByRole($user);

        // Menampilkan halaman transaksi
        return view('admin.transaksi.index', compact('transaksi'));
        staging
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
        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        $user = auth()->user();

        if ($user->role !== User::ROLE_PEMBELI) {
            return back()->with('error', 'Hanya pembeli yang bisa membeli kuda.');
        }

        $kuda = \App\Models\Kuda::with(['peternakan', 'lisensi'])->findOrFail($request->id_kuda);

        if ($kuda->status_jual !== Kuda::STATUS_TERSEDIA) {
            return back()->with('error', 'Kuda ini sudah tidak tersedia.');
        }

        $idLisensi = null;
        if ($request->pakai_lisensi == 1 && $kuda->lisensi) {
            $idLisensi = $kuda->lisensi->id_lisensi;
        }

        Transaksi::create([
            'status_transaksi' => Transaksi::STATUS_PENDING,
          
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mencegah selain pembeli membuat transaksi pembelian
        if ($user->role !== 'pembeli') {
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
        if ($kuda->status_jual !== 'tersedia') {
            return back()->with('error', 'Kuda ini sudah tidak tersedia.');
        }

        // Mencegah transaksi jika data peternakan kuda tidak tersedia
        if (!$kuda->peternakan) {
            return back()->with('error', 'Data peternakan kuda tidak ditemukan.');
        }

        // Menentukan apakah pembelian menggunakan lisensi
        $idLisensi = $this->getLisensiPembelian($validated['pakai_lisensi'], $kuda);

        // Membuat transaksi dengan status awal pending
        Transaksi::create([
            'status_transaksi' => 'pending',
        staging
            'tgl_transaksi'    => now(),
            'harga_final'      => $kuda->harga_buka,
            'id_kuda'          => $kuda->id_kuda,
            'id_lisensi'       => $idLisensi,
            'id_pembeli'       => $user->id_user,
            'id_penjual'       => $kuda->peternakan->id_user,
        ]);

        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        return redirect()->route('transaksi.index')->with('success', 'Pengajuan pembelian berhasil dikirim ke penjual.');

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Pengajuan pembelian berhasil dikirim ke penjual.');
        staging
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
        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
        $user = auth()->user();
        $transaksi->load('kuda');

        if ($user->role !== User::ROLE_PETERNAK || $transaksi->id_penjual !== $user->id_user) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk transaksi ini.');
        }

        if ($transaksi->status_transaksi !== Transaksi::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Transaksi ini sudah diproses.');
        }

        if ($request->aksi === 'terima') {
            $transaksi->update([
                'status_transaksi' => Transaksi::STATUS_SELESAI,
            ]);
            $transaksi->kuda->update([
                'status_jual' => Kuda::STATUS_TERJUAL,
            ]);
        }

        if ($request->aksi === 'tolak') {
            $transaksi->update([
                'status_transaksi' => Transaksi::STATUS_DIBATALKAN,
            ]);
        }

        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui.');

        // Mengambil user yang sedang login
        $user = auth()->user();

        // Memvalidasi aksi transaksi
        $validated = $request->validate([
            'aksi' => 'required|in:terima,tolak',
        ]);

        // Mengambil transaksi beserta data kuda
        $transaksi = Transaksi::with('kuda')->findOrFail($id);

        // Mencegah selain peternak pemilik transaksi memproses transaksi
        if (
            $user->role !== 'peternak'
            || $transaksi->id_penjual !== $user->id_user
        ) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki akses untuk transaksi ini.');
        }

        // Mencegah transaksi diproses lebih dari satu kali
        if ($transaksi->status_transaksi !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Transaksi ini sudah diproses.');
        }

        // Menerima transaksi dan mengubah status kuda menjadi terjual
        if ($validated['aksi'] === 'terima') {
            $transaksi->update([
                'status_transaksi' => 'selesai',
            ]);

            if ($transaksi->kuda) {
                $transaksi->kuda->update([
                    'status_jual' => 'terjual',
                ]);
            }
        }

        // Menolak transaksi dan mengubah status transaksi menjadi dibatalkan
        if ($validated['aksi'] === 'tolak') {
            $transaksi->update([
                'status_transaksi' => 'dibatalkan',
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
      staging
    }

    private function getTransaksiByRole($user)
    {
        // Query dasar untuk mengambil transaksi beserta relasinya
        $query = Transaksi::with([
            'kuda',
            'pembeli',
            'penjual',
            'lisensi'
        ])->latest();

        // Admin dapat melihat semua transaksi
        if ($user->role === 'admin') {
            return $query->get();
        }

        // Pembeli hanya melihat transaksi miliknya sendiri
        if ($user->role === 'pembeli') {
            return $query
                ->where('id_pembeli', $user->id_user)
                ->get();
        }

        // Peternak hanya melihat transaksi penjualan miliknya
        if ($user->role === 'peternak') {
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
