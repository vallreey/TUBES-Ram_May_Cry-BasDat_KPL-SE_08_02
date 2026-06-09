<?php

namespace App\Http\Controllers;

use App\Models\Kuda;
use App\Models\Transaksi;
use App\Models\Peternakan;
use App\Models\KawinSilang;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === User::ROLE_ADMIN) {
            $totalKuda = Kuda::count();
        }

        elseif ($user->role === User::ROLE_PETERNAK) {
            $totalKuda = Kuda::whereHas('peternakan', function ($q) use ($user) {
                $q->where('id_user', $user->id_user);
            })->count();
        }

        elseif ($user->role === User::ROLE_PEMBELI) {
            $totalKuda = Kuda::whereHas('transaksi', function ($q) use ($user) {
                $q->where('id_pembeli', $user->id_user)
                  ->where('status_transaksi', Transaksi::STATUS_SELESAI);
            })->count();
        }

        else {
            $totalKuda = 0;
        }

        $totalBreeding   = KawinSilang::count();
        $totalPeternakan = Peternakan::count();

        $queryTransaksi = Transaksi::with(['kuda', 'pembeli', 'penjual'])->latest();

        if ($user->role === User::ROLE_ADMIN) {
            $totalTransaksi = Transaksi::count();
            $transaksiTerbaru = $queryTransaksi->take(5)->get();
        } 
        elseif ($user->role === User::ROLE_PEMBELI) {
            $totalTransaksi = Transaksi::where('id_pembeli', $user->id_user)->count();
            $transaksiTerbaru = $queryTransaksi->where('id_pembeli', $user->id_user)->take(5)->get();
        } 
        elseif ($user->role === User::ROLE_PETERNAK) {
            $totalTransaksi = Transaksi::where('id_penjual', $user->id_user)->count();
            $transaksiTerbaru = $queryTransaksi->where('id_penjual', $user->id_user)->take(5)->get();
        } 
        else {
            $totalTransaksi = 0;
            $transaksiTerbaru = collect([]);
        }

        $breedingTerbaru = KawinSilang::with(['kudaBetina', 'kudaJantan'])
            ->latest()->take(5)->get();

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
