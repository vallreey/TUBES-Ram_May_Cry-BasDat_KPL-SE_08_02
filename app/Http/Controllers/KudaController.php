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
        return view('admin.kuda.create');
    }

    public function store(Request $request)
    {
        Kuda::create($request->all());

        return redirect()->route('kuda.index');
    }

    public function edit($id)
    {
        $kuda = Kuda::findOrFail($id);

        return view('admin.kuda.edit', compact('kuda'));
    }

    public function update(Request $request, $id)
    {
        $kuda = Kuda::findOrFail($id);
        $kuda->update($request->all());

        return redirect()->route('kuda.index');
    }

    public function destroy($id)
    {
        $kuda = Kuda::findOrFail($id);
        $kuda->delete();

        return redirect()->route('kuda.index');
    }
}