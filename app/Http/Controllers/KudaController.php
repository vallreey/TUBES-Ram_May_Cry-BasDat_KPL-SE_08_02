<?php

namespace App\Http\Controllers;

use App\Models\Kuda;
use Illuminate\Http\Request;

class KudaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $kuda = Kuda::with(['peternakan', 'lisensi'])->latest()->get();
        } elseif ($user->role === 'peternak') {
            $kuda = Kuda::with(['peternakan', 'lisensi'])
                ->whereHas('peternakan', function ($q) use ($user) {
                    $q->where('id_user', $user->id_user);
                })
                ->latest()
                ->get();
        } else {
            $kuda = Kuda::with(['peternakan', 'lisensi'])
                ->whereHas('transaksi', function ($q) use ($user) {
                    $q->where('id_pembeli', $user->id_user)
                      ->where('status_transaksi', 'selesai');
                })
                ->latest()
                ->get();
        }

        $page = 'owned';

        return view('admin.kuda.index', compact('kuda', 'page'));
    }

    public function tersedia()
    {
        $kuda = Kuda::with(['peternakan', 'lisensi'])
            ->where('status_jual', 'tersedia')
            ->latest()
            ->get();

        $page = 'tersedia';

        return view('admin.kuda.index', compact('kuda', 'page'));
    }

    public function terjual()
    {
        $kuda = Kuda::with(['peternakan', 'lisensi'])
            ->where('status_jual', 'terjual')
            ->latest()
            ->get();

        $page = 'terjual';

        return view('admin.kuda.index', compact('kuda', 'page'));
    }

    public function breeding()
    {
        $kuda = Kuda::with(['peternakan', 'lisensi'])
            ->where('status_jual', 'breeding')
            ->latest()
            ->get();

        $page = 'breeding';

        return view('admin.kuda.index', compact('kuda', 'page'));
    }

    public function create()
    {
        if (auth()->user()->role === 'pembeli') {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa menambahkan kuda karena tidak memiliki peternakan.');
        }

        return view('kuda.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'pembeli') {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa menambahkan kuda.');
        }

        $peternakan = \App\Models\Peternakan::where('id_user', $user->id_user)->first();

        if (!$peternakan) {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Anda belum memiliki peternakan.');
        }

        Kuda::create([
            'nama_kuda'     => $request->nama_kuda,
            'jenis_kuda'    => $request->jenis_kuda,
            'status_jual'   => $request->status_jual,
            'harga_buka'    => $request->harga_buka,
            'id_peternakan' => $peternakan->id_peternakan,
            'id_ibu'        => $request->id_ibu,
            'id_ayah'       => $request->id_ayah,
        ]);

        return redirect()
            ->route('kuda.index')
            ->with('success', 'Data kuda berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = auth()->user();

        $kuda = Kuda::with('peternakan')->findOrFail($id);

        if ($user->role === 'pembeli') {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa mengedit data kuda.');
        }

        if (
            $user->role === 'peternak'
            && (
                !$kuda->peternakan
                || $kuda->peternakan->id_user !== $user->id_user
                || $kuda->status_jual === 'terjual'
            )
        ) {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Anda tidak bisa mengedit kuda ini.');
        }

        return view('admin.kuda.edit', compact('kuda'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $kuda = Kuda::with('peternakan')->findOrFail($id);

        if ($user->role === 'pembeli') {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa mengubah data kuda.');
        }

        if (
            $user->role === 'peternak'
            && (
                !$kuda->peternakan
                || $kuda->peternakan->id_user !== $user->id_user
                || $kuda->status_jual === 'terjual'
            )
        ) {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Anda tidak bisa mengubah data kuda ini.');
        }

        $kuda->update($request->all());

        return redirect()
            ->route('kuda.index')
            ->with('success', 'Data kuda berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $kuda = Kuda::with('peternakan')->findOrFail($id);

        if ($user->role === 'pembeli') {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Pembeli tidak bisa menghapus data kuda.');
        }

        if (
            $user->role === 'peternak'
            && (
                !$kuda->peternakan
                || $kuda->peternakan->id_user !== $user->id_user
                || $kuda->status_jual === 'terjual'
            )
        ) {
            return redirect()
                ->route('kuda.index')
                ->with('error', 'Anda tidak bisa menghapus kuda ini.');
        }

        $kuda->delete();

        return redirect()
            ->route('kuda.index')
            ->with('success', 'Data kuda berhasil dihapus.');
    }
}
