<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
{
    $user = auth()->user();

    $query = Transaksi::with([
        'kuda',
        'pembeli',
        'penjual'
    ])->latest();

    // ADMIN bisa melihat semua transaksi
    if ($user->role === 'admin') {
        $transaksi = $query->get();
    }

    // PEMBELI hanya melihat transaksi miliknya sendiri
    elseif ($user->role === 'pembeli') {
        $transaksi = $query
            ->where('id_pembeli', $user->id_user)
            ->get();
    }

    // PETERNAK hanya melihat transaksi yang dia lakukan sebagai penjual
    elseif ($user->role === 'peternak') {
        $transaksi = $query
            ->where('id_penjual', $user->id_user)
            ->get();
    }

    // Jika role tidak dikenal
    else {
        $transaksi = collect([]);
    }

    return view('admin.transaksi.index', compact('transaksi'));
}

    public function create()
    {
        return view('admin.transaksi.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return view('admin.transaksi.show');
    }

    public function edit($id)
    {
        return view('admin.transaksi.edit');
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
