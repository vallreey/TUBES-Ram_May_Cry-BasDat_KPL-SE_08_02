<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kuda;

class KudaController extends Controller
{
    public function index()
    {
        $kuda = Kuda::all(); // Ambil semua data kuda
        return view('kuda.index', compact('kuda'));
    }

    public function create()
    {
        return view('kuda.create');
    }

    public function store(Request $request)
    {
        Kuda::create($request->all());
        return redirect()->route('kuda.index');
    }

    public function edit($id)
    {
        $kuda = Kuda::findOrFail($id);
        return view('kuda.edit', compact('kuda'));
    }

    public function update(Request $request, $id)
    {
        $kuda = Kuda::findOrFail($id);
        $kuda->update($request->all());
        return redirect()->route('kuda.index');
    }

    public function destroy($id)
    {
        $kuda = Kuda::findOrFail($id);
        $kuda->delete();
        return redirect()->route('kuda.index');
    }
}