<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kuda;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransaksiApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Transaksi::with(['kuda', 'lisensi', 'pembeli', 'penjual']);

        if ($request->filled('status_transaksi')) {
            $query->where('status_transaksi', $request->status_transaksi);
        }

        if ($request->filled('id_pembeli')) {
            $query->where('id_pembeli', $request->id_pembeli);
        }

        if ($request->filled('id_penjual')) {
            $query->where('id_penjual', $request->id_penjual);
        }

        $transaksi = $query->latest()->paginate(10);

        return $this->successResponse($transaksi, 'Data transaksi berhasil diambil');
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['kuda', 'lisensi', 'pembeli', 'penjual'])->find($id);

        if (!$transaksi) {
            return $this->errorResponse('Transaksi tidak ditemukan', 404);
        }

        return $this->successResponse($transaksi, 'Detail transaksi berhasil diambil');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_kuda' => 'required|exists:kuda,id_kuda',
                'id_lisensi' => 'nullable|exists:lisensi,id_lisensi',
                'id_pembeli' => 'required|exists:users,id_user',
                'id_penjual' => 'required|exists:users,id_user',
                'harga_final' => 'required|numeric|min:0',
                'status_transaksi' => 'nullable|in:pending,selesai,dibatalkan',
                'tgl_transaksi' => 'nullable|date',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        $transaksi = Transaksi::create([
            'status_transaksi' => $validated['status_transaksi'] ?? 'pending',
            'tgl_transaksi' => $validated['tgl_transaksi'] ?? now(),
            'harga_final' => $validated['harga_final'],
            'id_kuda' => $validated['id_kuda'],
            'id_lisensi' => $validated['id_lisensi'] ?? null,
            'id_pembeli' => $validated['id_pembeli'],
            'id_penjual' => $validated['id_penjual'],
        ]);

        return $this->successResponse(
            $transaksi->load(['kuda', 'lisensi', 'pembeli', 'penjual']),
            'Transaksi berhasil ditambahkan',
            201
        );
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::with('kuda')->find($id);

        if (!$transaksi) {
            return $this->errorResponse('Transaksi tidak ditemukan', 404);
        }

        try {
            $validated = $request->validate([
                'status_transaksi' => ['sometimes', 'required', Rule::in(['pending', 'selesai', 'dibatalkan'])],
                'tgl_transaksi' => 'nullable|date',
                'harga_final' => 'sometimes|required|numeric|min:0',
                'id_kuda' => 'sometimes|required|exists:kuda,id_kuda',
                'id_lisensi' => 'nullable|exists:lisensi,id_lisensi',
                'id_pembeli' => 'sometimes|required|exists:users,id_user',
                'id_penjual' => 'sometimes|required|exists:users,id_user',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        DB::transaction(function () use ($transaksi, $validated) {
            $transaksi->update($validated);

            if (($validated['status_transaksi'] ?? null) === 'selesai' && $transaksi->kuda) {
                $transaksi->kuda->update(['status_jual' => 'terjual']);
            }
        });

        return $this->successResponse(
            $transaksi->refresh()->load(['kuda', 'lisensi', 'pembeli', 'penjual']),
            'Transaksi berhasil diperbarui'
        );
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return $this->errorResponse('Transaksi tidak ditemukan', 404);
        }

        $transaksi->delete();

        return $this->successResponse(null, 'Transaksi berhasil dihapus');
    }
}
