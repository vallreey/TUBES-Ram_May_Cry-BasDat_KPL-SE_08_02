<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        // Mengambil data transaksi beserta relasinya
        $query = Transaksi::with(['kuda', 'lisensi', 'pembeli', 'penjual']);

        // Filter transaksi berdasarkan status
        if ($request->filled('status_transaksi')) {
            $query->where('status_transaksi', $request->status_transaksi);
        }

        // Filter transaksi berdasarkan pembeli
        if ($request->filled('id_pembeli')) {
            $query->where('id_pembeli', $request->id_pembeli);
        }

        // Filter transaksi berdasarkan penjual
        if ($request->filled('id_penjual')) {
            $query->where('id_penjual', $request->id_penjual);
        }

        // Mengambil data transaksi dengan pagination
        $transaksi = $query->latest()->paginate(10);

        // Mengembalikan response data transaksi
        return $this->successResponse($transaksi, 'Data transaksi berhasil diambil');
    }

    public function show($id)
    {
        // Mengambil detail transaksi berdasarkan ID
        $transaksi = Transaksi::with(['kuda', 'lisensi', 'pembeli', 'penjual'])->find($id);

        // Mengembalikan error jika transaksi tidak ditemukan
        if (!$transaksi) {
            return $this->errorResponse('Transaksi tidak ditemukan', 404);
        }

        // Mengembalikan response detail transaksi
        return $this->successResponse($transaksi, 'Detail transaksi berhasil diambil');
    }

    public function store(Request $request)
    {
        try {
        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
            $validated = $request->validate([
                'id_kuda' => 'required|exists:kuda,id_kuda',
                'id_lisensi' => 'nullable|exists:lisensi,id_lisensi',
                'id_pembeli' => 'required|exists:users,id_user',
                'id_penjual' => 'required|exists:users,id_user',
                'harga_final' => 'required|numeric|min:0',
                'status_transaksi' => 'nullable|in:' . Transaksi::STATUS_PENDING . ',' . Transaksi::STATUS_SELESAI . ',' . Transaksi::STATUS_DIBATALKAN,
                'tgl_transaksi' => 'nullable|date',
            ]);

            // Memvalidasi data transaksi sebelum disimpan
            $validated = $this->validateTransaksiData($request);
        staging
        } catch (ValidationException $e) {
            // Mengembalikan error jika validasi gagal
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        // Menyimpan data transaksi baru
        $transaksi = Transaksi::create([
            'status_transaksi' => $validated['status_transaksi'] ?? Transaksi::STATUS_PENDING,
            'tgl_transaksi' => $validated['tgl_transaksi'] ?? now(),
            'harga_final' => $validated['harga_final'],
            'id_kuda' => $validated['id_kuda'],
            'id_lisensi' => $validated['id_lisensi'] ?? null,
            'id_pembeli' => $validated['id_pembeli'],
            'id_penjual' => $validated['id_penjual'],
        ]);

        // Mengembalikan response transaksi berhasil ditambahkan
        return $this->successResponse(
            $transaksi->load(['kuda', 'lisensi', 'pembeli', 'penjual']),
            'Transaksi berhasil ditambahkan',
            201
        );
    }

    public function update(Request $request, $id)
    {
        // Mengambil data transaksi yang akan diperbarui
        $transaksi = Transaksi::with('kuda')->find($id);

        // Mengembalikan error jika transaksi tidak ditemukan
        if (!$transaksi) {
            return $this->errorResponse('Transaksi tidak ditemukan', 404);
        }

        try {
        Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif
            $validated = $request->validate([
                'status_transaksi' => ['sometimes', 'required', Rule::in([Transaksi::STATUS_PENDING, Transaksi::STATUS_SELESAI, Transaksi::STATUS_DIBATALKAN])],
                'tgl_transaksi' => 'nullable|date',
                'harga_final' => 'sometimes|required|numeric|min:0',
                'id_kuda' => 'sometimes|required|exists:kuda,id_kuda',
                'id_lisensi' => 'nullable|exists:lisensi,id_lisensi',
                'id_pembeli' => 'sometimes|required|exists:users,id_user',
                'id_penjual' => 'sometimes|required|exists:users,id_user',
            ]);

            // Memvalidasi data transaksi yang akan diperbarui
            $validated = $this->validateTransaksiUpdateData($request);
        staging
        } catch (ValidationException $e) {
            // Mengembalikan error jika validasi gagal
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        // Memperbarui transaksi dan status kuda dalam satu proses
        DB::transaction(function () use ($transaksi, $validated) {
            $transaksi->update($validated);

            if (($validated['status_transaksi'] ?? null) === Transaksi::STATUS_SELESAI && $transaksi->kuda) {
                $transaksi->kuda->update(['status_jual' => Kuda::STATUS_TERJUAL]);
            }
        });

        // Mengembalikan response transaksi berhasil diperbarui
        return $this->successResponse(
            $transaksi->refresh()->load(['kuda', 'lisensi', 'pembeli', 'penjual']),
            'Transaksi berhasil diperbarui'
        );
    }

    public function destroy($id)
    {
        // Mengambil data transaksi yang akan dihapus
        $transaksi = Transaksi::find($id);

        // Mengembalikan error jika transaksi tidak ditemukan
        if (!$transaksi) {
            return $this->errorResponse('Transaksi tidak ditemukan', 404);
        }

        // Menghapus data transaksi
        $transaksi->delete();

        // Mengembalikan response transaksi berhasil dihapus
        return $this->successResponse(null, 'Transaksi berhasil dihapus');
    }

    private function validateTransaksiData(Request $request)
    {
        // Validasi untuk menambah data transaksi
        return $request->validate([
            'id_kuda' => 'required|exists:kuda,id_kuda',
            'id_lisensi' => 'nullable|exists:lisensi,id_lisensi',
            'id_pembeli' => 'required|exists:users,id_user',
            'id_penjual' => 'required|exists:users,id_user',
            'harga_final' => 'required|numeric|min:0',
            'status_transaksi' => 'nullable|in:pending,selesai,dibatalkan',
            'tgl_transaksi' => 'nullable|date',
        ]);
    }

    private function validateTransaksiUpdateData(Request $request)
    {
        // Validasi untuk memperbarui data transaksi
        return $request->validate([
            'status_transaksi' => ['sometimes', 'required', Rule::in(['pending', 'selesai', 'dibatalkan'])],
            'tgl_transaksi' => 'nullable|date',
            'harga_final' => 'sometimes|required|numeric|min:0',
            'id_kuda' => 'sometimes|required|exists:kuda,id_kuda',
            'id_lisensi' => 'nullable|exists:lisensi,id_lisensi',
            'id_pembeli' => 'sometimes|required|exists:users,id_user',
            'id_penjual' => 'sometimes|required|exists:users,id_user',
        ]);
    }
}
