<?php

namespace App\Http\Controllers;

use App\Models\Peternakan;

class PeternakanController extends Controller
{
    public function index()
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil data peternakan beserta relasi user, kuda, dan lisensi
        $query = Peternakan::with([
            'user',
            'kuda',
            'kuda.lisensi'
        ])->latest();

        // Peternak hanya bisa melihat peternakan miliknya sendiri
        if ($user->role === 'peternak') {
            $query->where('id_user', $user->id_user);
        }

        // Menjalankan query dan mengambil data peternakan
        $peternakan = $query->get();

        // Mengirim data peternakan ke halaman index
        return view('admin.peternakan.index', compact('peternakan'));
    }
}
