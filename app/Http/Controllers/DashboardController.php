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
        // Mengambil user yang sedang login
        $user = auth()->user();

        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
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

        // Mengambil total kuda berdasarkan role user
        $totalKuda = $this->getTotalKudaByRole($user);

        // Mengambil total transaksi berdasarkan role user
        $totalTransaksi = $this->getTotalTransaksiByRole($user);

        // Mengambil total data breeding
        $totalBreeding = KawinSilang::count();

        // Mengambil total data peternakan
        $totalPeternakan = Peternakan::count();

        // Mengambil transaksi terbaru berdasarkan role user
        $transaksiTerbaru = $this->getTransaksiTerbaruByRole($user);

        // Mengambil data kawin silang terbaru
        $breedingTerbaru = KawinSilang::with(['kudaBetina', 'kudaJantan'])
            ->latest()
            ->take(5)
            ->get();

        // Menampilkan halaman dashboard
        return view('admin.dashboard', compact(
            'totalKuda',
            'totalTransaksi',
            'totalBreeding',
            'totalPeternakan',
            'transaksiTerbaru',
            'breedingTerbaru'
        ));
    }

    private function getTotalKudaByRole($user)
    {
        // Admin dapat menghitung semua kuda
        if ($user->role === 'admin') {
            return Kuda::count();
        }

        // Peternak hanya menghitung kuda dari peternakannya sendiri
        if ($user->role === 'peternak') {
            return Kuda::whereHas('peternakan', function ($q) use ($user) {
                $q->where('id_user', $user->id_user);
            })->count();
        }

        // Pembeli hanya menghitung kuda yang transaksinya selesai
        if ($user->role === 'pembeli') {
            return Kuda::whereHas('transaksi', function ($q) use ($user) {
                $q->where('id_pembeli', $user->id_user)
                  ->where('status_transaksi', 'selesai');
            })->count();
        }

        // Mengembalikan nol jika role tidak dikenali
        return 0;
    }

    private function getTotalTransaksiByRole($user)
    {
        // Admin dapat menghitung semua transaksi
        if ($user->role === 'admin') {
            return Transaksi::count();
        }

        // Pembeli hanya menghitung transaksi miliknya sendiri
        if ($user->role === 'pembeli') {
            return Transaksi::where('id_pembeli', $user->id_user)->count();
        }

        // Peternak hanya menghitung transaksi penjualan miliknya
        if ($user->role === 'peternak') {
            return Transaksi::where('id_penjual', $user->id_user)->count();
        }

        // Mengembalikan nol jika role tidak dikenali
        return 0;
    }

    private function getTransaksiTerbaruByRole($user)
    {
        // Query dasar transaksi terbaru beserta relasinya
        $query = Transaksi::with([
            'kuda',
            'pembeli',
            'penjual'
        ])->latest();

        // Admin dapat melihat semua transaksi terbaru
        if ($user->role === 'admin') {
            return $query->take(5)->get();
        }

        // Pembeli hanya melihat transaksi terbarunya sendiri
        if ($user->role === 'pembeli') {
            return $query
                ->where('id_pembeli', $user->id_user)
                ->take(5)
                ->get();
        }

        // Peternak hanya melihat transaksi penjualan terbarunya sendiri
        if ($user->role === 'peternak') {
            return $query
                ->where('id_penjual', $user->id_user)
                ->take(5)
                ->get();
        }

        // Mengembalikan data kosong jika role tidak dikenali
        return collect([]);
        staging
    }
}
