<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kuda;

class TransaksiController extends Controller
{
    public function index()
    {
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
    }

    public function create()
    {
        return view('admin.transaksi.create');
    }

    public function store(Request $request)
    {
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
            'tgl_transaksi'    => now(),
            'harga_final'      => $kuda->harga_buka,
            'id_kuda'          => $kuda->id_kuda,
            'id_lisensi'       => $idLisensi,
            'id_pembeli'       => $user->id_user,
            'id_penjual'       => $kuda->peternakan->id_user,
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Pengajuan pembelian berhasil dikirim ke penjual.');
    }

    public function show($id)
    {
        return view('admin.transaksi.show');
    }

    public function edit($id)
    {
        return view('admin.transaksi.edit');
    }

    public function update(Request $request, Transaksi $transaksi)
    {
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
    }

    public function destroy($id)
    {
        //
    }
}
