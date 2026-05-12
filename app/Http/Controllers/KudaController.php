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
        return view('admin.dashboard');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return view('admin.dashboard');
    }

    public function edit($id)
    {
        return view('admin.dashboard');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
