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

        // Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif

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
        if ($user->role === User::ROLE_ADMIN) {
            return Kuda::count();
        }

        // Peternak hanya menghitung kuda dari peternakannya sendiri
        if ($user->role === User::ROLE_PETERNAK) {
            return Kuda::whereHas('peternakan', function ($q) use ($user) {
                $q->where('id_user', $user->id_user);
            })->count();
        }

        // Pembeli hanya menghitung kuda yang transaksinya selesai
        if ($user->role === User::ROLE_PEMBELI) {
            return Kuda::whereHas('transaksi', function ($q) use ($user) {
                $q->where('id_pembeli', $user->id_user)
                  ->where('status_transaksi', Transaksi::STATUS_SELESAI);
            })->count();
        }

        // Mengembalikan nol jika role tidak dikenali
        return 0;
    }

    private function getTotalTransaksiByRole($user)
    {
        // Admin dapat menghitung semua transaksi
        if ($user->role === User::ROLE_ADMIN) {
            return Transaksi::count();
        }

        // Pembeli hanya menghitung transaksi miliknya sendiri
        if ($user->role === User::ROLE_PEMBELI) {
            return Transaksi::where('id_pembeli', $user->id_user)->count();
        }

        // Peternak hanya menghitung transaksi penjualan miliknya
        if ($user->role === User::ROLE_PETERNAK) {
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
            'penjual',
        ])->latest();

        // Admin dapat melihat semua transaksi terbaru
        if ($user->role === User::ROLE_ADMIN) {
            return $query->take(5)->get();
        }

        // Pembeli hanya melihat transaksi terbarunya sendiri
        if ($user->role === User::ROLE_PEMBELI) {
            return $query
                ->where('id_pembeli', $user->id_user)
                ->take(5)
                ->get();
        }

        // Peternak hanya melihat transaksi penjualan terbarunya sendiri
        if ($user->role === User::ROLE_PETERNAK) {
            return $query
                ->where('id_penjual', $user->id_user)
                ->take(5)
                ->get();
        }

        // staging

        // Mengembalikan data kosong jika role tidak dikenali
        return collect([]);
    }
}
