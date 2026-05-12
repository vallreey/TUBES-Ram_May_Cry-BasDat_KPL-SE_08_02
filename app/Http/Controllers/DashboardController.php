<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Nanti disambungkan ke model masing-masing
        // Contoh: $totalKuda = \App\Models\Kuda::count();

        return view('admin.dashboard', [
            'totalKuda'       => 0,
            'totalTransaksi'  => 0,
            'totalBreeding'   => 0,
            'totalPeternakan' => 0,
            'transaksiTerbaru' => collect([]),
            'breedingTerbaru'  => collect([]),
        ]);
    }
}
