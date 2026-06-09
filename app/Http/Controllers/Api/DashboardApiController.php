<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KawinSilang;
use App\Models\Kuda;
use App\Models\Peternakan;
use App\Models\Transaksi;
use App\Models\User;

class DashboardApiController extends Controller
{
    use ApiResponse;

    public function summary()
    {
        // Mengambil ringkasan total data untuk dashboard API
        $summary = [
            'total_user' => User::count(),
            'total_peternakan' => Peternakan::count(),
            'total_kuda' => Kuda::count(),
            'total_kuda_tersedia' => Kuda::where('status_jual', 'tersedia')->count(),
            'total_kuda_terjual' => Kuda::where('status_jual', 'terjual')->count(),
            'total_kuda_breeding' => Kuda::where('status_jual', 'breeding')->count(),
            'total_transaksi' => Transaksi::count(),
            'total_kawin_silang' => KawinSilang::count(),
        ];

        // Mengembalikan response ringkasan dashboard
        return $this->successResponse(
            $summary,
            'Ringkasan dashboard berhasil diambil'
        );
    }
}
