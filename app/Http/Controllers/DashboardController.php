<?php

namespace App\Http\Controllers;

use App\Models\Kuda;
use App\Models\Transaksi;
use App\Models\Peternakan;
use App\Models\KawinSilang;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

    if ($user->role === 'admin') {

        $totalKuda = Kuda::count();
    }

    elseif ($user->role === 'peternak') {

        $totalKuda = Kuda::whereHas('peternakan', function ($q) use ($user) {
            $q->where('id_user', $user->id_user);
        })->count();
    }

    elseif ($user->role === 'pembeli') {

        $totalKuda = Kuda::whereHas('transaksi', function ($q) use ($user) {
            $q->where('id_pembeli', $user->id_user)
            ->where('status_transaksi', 'selesai');
        })->count();
    }

    else {

        $totalKuda = 0;
    }
        $totalBreeding   = KawinSilang::count();
        $totalPeternakan = Peternakan::count();

        $queryTransaksi = Transaksi::with([
            'kuda',
            'pembeli',
            'penjual'
        ])->latest();

        // ADMIN melihat semua transaksi
        if ($user->role === 'admin') {
            $totalTransaksi = Transaksi::count();

            $transaksiTerbaru = $queryTransaksi
                ->take(5)
                ->get();
        }

        // PEMBELI hanya melihat transaksi miliknya sendiri
        elseif ($user->role === 'pembeli') {
            $totalTransaksi = Transaksi::where('id_pembeli', $user->id_user)->count();

            $transaksiTerbaru = $queryTransaksi
                ->where('id_pembeli', $user->id_user)
                ->take(5)
                ->get();
        }

        // PETERNAK hanya melihat transaksi penjualan miliknya
        elseif ($user->role === 'peternak') {
            $totalTransaksi = Transaksi::where('id_penjual', $user->id_user)->count();

            $transaksiTerbaru = $queryTransaksi
                ->where('id_penjual', $user->id_user)
                ->take(5)
                ->get();
        }

        else {
            $totalTransaksi = 0;
            $transaksiTerbaru = collect([]);
        }

        $breedingTerbaru = KawinSilang::with(['kudaBetina', 'kudaJantan'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalKuda',
            'totalTransaksi',
            'totalBreeding',
            'totalPeternakan',
            'transaksiTerbaru',
            'breedingTerbaru'
        ));
    }
}
