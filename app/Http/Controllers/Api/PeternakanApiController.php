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
        $peternakan = Peternakan::with(['user', 'kuda.lisensi'])
            ->latest()
            ->paginate(10);

        return $this->successResponse($peternakan, 'Data peternakan berhasil diambil');
    }

    public function show($id)
    {
        $peternakan = Peternakan::with(['user', 'kuda.lisensi'])->find($id);

        if (!$peternakan) {
            return $this->errorResponse('Peternakan tidak ditemukan', 404);
        }

        return $this->successResponse($peternakan, 'Detail peternakan berhasil diambil');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_peternakan' => 'required|string|max:100',
                'kapasitas_kandang' => 'required|integer|min:0',
                'lokasi_map' => 'nullable|string|max:255',
                'alamat_lengkap' => 'nullable|string',
                'id_user' => 'required|exists:users,id_user',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        $peternakan = Peternakan::create($validated);

        return $this->successResponse(
            $peternakan->load('user'),
            'Peternakan berhasil ditambahkan',
            201
        );
    }

    public function update(Request $request, $id)
    {
        $peternakan = Peternakan::find($id);

        if (!$peternakan) {
            return $this->errorResponse('Peternakan tidak ditemukan', 404);
        }

        try {
            $validated = $request->validate([
                'nama_peternakan' => 'sometimes|required|string|max:100',
                'kapasitas_kandang' => 'sometimes|required|integer|min:0',
                'lokasi_map' => 'nullable|string|max:255',
                'alamat_lengkap' => 'nullable|string',
                'id_user' => ['sometimes', 'required', Rule::exists('users', 'id_user')],
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        $peternakan->update($validated);

        return $this->successResponse(
            $peternakan->load('user'),
            'Peternakan berhasil diperbarui'
        );
    }

    public function destroy($id)
    {
        $peternakan = Peternakan::find($id);

        if (!$peternakan) {
            return $this->errorResponse('Peternakan tidak ditemukan', 404);
        }

        $peternakan->delete();

        return $this->successResponse(null, 'Peternakan berhasil dihapus');
    }
}
