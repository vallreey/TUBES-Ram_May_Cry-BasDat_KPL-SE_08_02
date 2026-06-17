<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peternakan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PeternakanApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        // Mengambil data peternakan beserta user dan lisensi kuda
        $peternakan = Peternakan::with(['user', 'kuda.lisensi'])
            ->latest()
            ->paginate(10);

        // Mengembalikan response data peternakan
        return $this->successResponse($peternakan, 'Data peternakan berhasil diambil');
    }

    public function show($id)
    {
        // Mengambil detail peternakan berdasarkan ID
        $peternakan = Peternakan::with(['user', 'kuda.lisensi'])->find($id);

        // Mengembalikan error jika peternakan tidak ditemukan
        if (!$peternakan) {
            return $this->errorResponse('Peternakan tidak ditemukan', 404);
        }

        // Mengembalikan response detail peternakan
        return $this->successResponse($peternakan, 'Detail peternakan berhasil diambil');
    }

    public function store(Request $request)
    {
        try {
            // Memvalidasi data peternakan sebelum disimpan
            $validated = $this->validatePeternakanData($request);
        } catch (ValidationException $e) {
            // Mengembalikan error jika validasi gagal
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        // Menyimpan data peternakan baru
        $peternakan = Peternakan::create($validated);

        // Mengembalikan response peternakan berhasil ditambahkan
        return $this->successResponse(
            $peternakan->load('user'),
            'Peternakan berhasil ditambahkan',
            201
        );
    }

    public function update(Request $request, $id)
    {
        // Mengambil data peternakan yang akan diperbarui
        $peternakan = Peternakan::find($id);

        // Mengembalikan error jika peternakan tidak ditemukan
        if (!$peternakan) {
            return $this->errorResponse('Peternakan tidak ditemukan', 404);
        }

        try {
            // Memvalidasi data peternakan yang akan diperbarui
            $validated = $this->validatePeternakanUpdateData($request);
        } catch (ValidationException $e) {
            // Mengembalikan error jika validasi gagal
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        // Memperbarui data peternakan
        $peternakan->update($validated);

        // Mengembalikan response peternakan berhasil diperbarui
        return $this->successResponse(
            $peternakan->load('user'),
            'Peternakan berhasil diperbarui'
        );
    }

    public function destroy($id)
    {
        // Mengambil data peternakan yang akan dihapus
        $peternakan = Peternakan::find($id);

        // Mengembalikan error jika peternakan tidak ditemukan
        if (!$peternakan) {
            return $this->errorResponse('Peternakan tidak ditemukan', 404);
        }

        // Menghapus data peternakan
        $peternakan->delete();

        // Mengembalikan response peternakan berhasil dihapus
        return $this->successResponse(null, 'Peternakan berhasil dihapus');
    }

    private function validatePeternakanData(Request $request)
    {
        // Validasi untuk menambah data peternakan
        return $request->validate([
            'nama_peternakan' => 'required|string|max:100',
            'kapasitas_kandang' => 'required|integer|min:0',
            'lokasi_map' => 'nullable|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'id_user' => 'required|exists:users,id_user',
        ]);
    }

    private function validatePeternakanUpdateData(Request $request)
    {
        // Validasi untuk memperbarui data peternakan
        return $request->validate([
            'nama_peternakan' => 'sometimes|required|string|max:100',
            'kapasitas_kandang' => 'sometimes|required|integer|min:0',
            'lokasi_map' => 'nullable|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'id_user' => ['sometimes', 'required', Rule::exists('users', 'id_user')],
        ]);
    }
}
