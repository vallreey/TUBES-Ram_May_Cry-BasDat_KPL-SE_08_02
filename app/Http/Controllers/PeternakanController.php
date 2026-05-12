<?php

namespace App\Http\Controllers;

use App\Models\Peternakan;

class PeternakanController extends Controller
{
    public function index()
    {
        $peternakan = Peternakan::with([
            'user',
            'kuda',
            'kuda.lisensi'
        ])
        ->latest()
        ->get();

        return view('admin.peternakan.index', compact('peternakan'));
    }
}
