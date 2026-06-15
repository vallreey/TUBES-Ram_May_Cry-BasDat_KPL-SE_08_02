<?php

namespace App\Http\Controllers;

use App\Models\Lisensi;
use App\Models\Kuda;
use App\Models\User;
use Illuminate\Http\Request;

class LisensiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin melihat semua lisensi
        if ($user->role === User::ROLE_ADMIN) {
            $lisensi = Lisensi::with(['kuda.peternakan', 'pengaju'])->latest()->get();
            return view('admin.lisensi.index', compact('lisensi'));
        }

        // Peternak melihat lisensi kuda milik peternakannya
        if ($user->role === User::ROLE_PETERNAK) {
            $peternakan = $user->peternakan;

            $lisensi = Lisensi::with(['kuda.peternakan', 'pengaju'])
                ->whereHas('kuda', function ($q) use ($peternakan) {
                    $q->where('id_peternakan', $peternakan?->id_peternakan);
                })
                ->latest()
                ->get();

            return view('admin.lisensi.index', compact('lisensi'));
        }

        // Pembeli melihat lisensi kuda yang sudah dibelinya
        if ($user->role === User::ROLE_PEMBELI) {
            $lisensi = Lisensi::with(['kuda.peternakan', 'pengaju'])
                ->whereHas('kuda.transaksi', function ($q) use ($user) {
                    $q->where('id_pembeli', $user->id_user)
                      ->where('status_transaksi', 'selesai');
                })
                ->latest()
                ->get();

            return view('admin.lisensi.index', compact('lisensi'));
        }

        return view('admin.lisensi.index', ['lisensi' => collect([])]);
    }

    // Form pengajuan lisensi baru (dari halaman kuda atau lisensi)
    public function create(Request $request)
    {
        $user = auth()->user();

        // Ambil kuda yang boleh diajukan lisensinya oleh user ini
        $kudaList = $this->getKudaBisaDiajukan($user);

        // Jika ada id_kuda dari query string (dari halaman kuda), pre-select
        $selectedKuda = $request->query('id_kuda');

        return view('admin.lisensi.create', compact('kudaList', 'selectedKuda'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'id_kuda'           => 'required|exists:kuda,id_kuda',
            'nomor_sertifikat'  => 'required|string|max:50|unique:lisensi,nomor_sertifikat',
            'penerbit'          => 'nullable|string|max:100',
            'tgl_terbit'        => 'nullable|date',
            'masa_berlaku'      => 'nullable|date|after_or_equal:tgl_terbit',
            'keaslian_ras'      => 'nullable|string|max:50',
            'riwayat_kesehatan' => 'nullable|string',
        ]);

        $kuda = Kuda::findOrFail($validated['id_kuda']);

        // Pastikan user berhak mengajukan lisensi untuk kuda ini
        if (!$this->bolehAjukanLisensi($user, $kuda)) {
            return redirect()->route('lisensi.index')
                ->with('error', 'Anda tidak berhak mengajukan lisensi untuk kuda ini.');
        }

        // Cek apakah kuda sudah punya lisensi pending atau approved
        $sudahAda = Lisensi::where('id_kuda', $kuda->id_kuda)
            ->whereIn('status', [Lisensi::STATUS_PENDING, Lisensi::STATUS_APPROVED])
            ->exists();

        if ($sudahAda) {
            return redirect()->back()
                ->with('error', 'Kuda ini sudah memiliki lisensi aktif atau sedang dalam proses pengajuan.')
                ->withInput();
        }

        Lisensi::create([
            'nomor_sertifikat'  => $validated['nomor_sertifikat'],
            'penerbit'          => $validated['penerbit'],
            'tgl_terbit'        => $validated['tgl_terbit'],
            'masa_berlaku'      => $validated['masa_berlaku'],
            'keaslian_ras'      => $validated['keaslian_ras'],
            'riwayat_kesehatan' => $validated['riwayat_kesehatan'],
            'id_kuda'           => $kuda->id_kuda,
            'status'            => Lisensi::STATUS_PENDING,
            'id_pengaju'        => $user->id_user,
        ]);

        return redirect()->route('lisensi.index')
            ->with('success', 'Pengajuan lisensi berhasil dikirim. Menunggu persetujuan admin.');
    }

    // Admin: approve lisensi
    public function approve(Request $request, $id)
    {
        $user = auth()->user();

        if ($user->role !== User::ROLE_ADMIN) {
            abort(403);
        }

        $lisensi = Lisensi::findOrFail($id);
        $lisensi->update([
            'status'        => Lisensi::STATUS_APPROVED,
            'catatan_admin' => $request->input('catatan_admin'),
        ]);

        return redirect()->route('lisensi.index')
            ->with('success', 'Lisensi berhasil disetujui.');
    }

    // Admin: decline lisensi
    public function decline(Request $request, $id)
    {
        $user = auth()->user();

        if ($user->role !== User::ROLE_ADMIN) {
            abort(403);
        }

        $lisensi = Lisensi::findOrFail($id);
        $lisensi->update([
            'status'        => Lisensi::STATUS_DECLINED,
            'catatan_admin' => $request->input('catatan_admin'),
        ]);

        return redirect()->route('lisensi.index')
            ->with('success', 'Lisensi berhasil ditolak.');
    }

    public function show($id)
    {
        $lisensi = Lisensi::with(['kuda.peternakan', 'pengaju'])->findOrFail($id);
        return view('admin.lisensi.show', compact('lisensi'));
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $lisensi = Lisensi::findOrFail($id);

        // Hanya admin atau pengaju sendiri (jika masih pending) yang bisa hapus
        $bolehHapus = $user->role === User::ROLE_ADMIN
            || ($lisensi->id_pengaju === $user->id_user && $lisensi->status === Lisensi::STATUS_PENDING);

        if (!$bolehHapus) {
            return redirect()->route('lisensi.index')
                ->with('error', 'Anda tidak berhak menghapus pengajuan lisensi ini.');
        }

        $lisensi->delete();

        return redirect()->route('lisensi.index')
            ->with('success', 'Pengajuan lisensi berhasil dihapus.');
    }

    // Cek apakah user boleh mengajukan lisensi untuk kuda tertentu
    private function bolehAjukanLisensi($user, $kuda)
    {
        // Admin boleh semua
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Peternak boleh untuk kuda di peternakannya
        if ($user->role === User::ROLE_PETERNAK) {
            return $kuda->peternakan && $kuda->peternakan->id_user === $user->id_user;
        }

        // Pembeli boleh untuk kuda yang sudah dibelinya (transaksi selesai)
        if ($user->role === User::ROLE_PEMBELI) {
            return $kuda->transaksi
                ->where('id_pembeli', $user->id_user)
                ->where('status_transaksi', 'selesai')
                ->isNotEmpty();
        }

        return false;
    }

    // Ambil daftar kuda yang bisa diajukan lisensinya oleh user
    private function getKudaBisaDiajukan($user)
    {
        if ($user->role === User::ROLE_ADMIN) {
            return Kuda::with('peternakan')->get();
        }

        if ($user->role === User::ROLE_PETERNAK) {
            $peternakan = $user->peternakan;
            return Kuda::where('id_peternakan', $peternakan?->id_peternakan)->get();
        }

        if ($user->role === User::ROLE_PEMBELI) {
            return Kuda::with('transaksi')
                ->whereHas('transaksi', function ($q) use ($user) {
                    $q->where('id_pembeli', $user->id_user)
                      ->where('status_transaksi', 'selesai');
                })
                ->get();
        }

        return collect([]);
    }
}
