<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kuda;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KudaApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Kuda::with(['peternakan.user', 'lisensi', 'ibu', 'ayah']);

        if ($request->filled('status_jual')) {
            $query->where('status_jual', $request->status_jual);
        }

        if ($request->filled('id_peternakan')) {
            $query->where('id_peternakan', $request->id_peternakan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kuda', 'like', "%{$search}%")
                    ->orWhere('jenis_kuda', 'like', "%{$search}%");
            });
        }

        $kuda = $query->latest()->paginate(10);

        return $this->successResponse($kuda, 'Data kuda berhasil diambil');
    }

    public function show($id)
    {
        $kuda = Kuda::with(['peternakan.user', 'lisensi', 'ibu', 'ayah', 'transaksi'])->find($id);

        if (!$kuda) {
            return $this->errorResponse('Kuda tidak ditemukan', 404);
        }

        return $this->successResponse($kuda, 'Detail kuda berhasil diambil');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_kuda' => 'required|string|max:100',
                'jenis_kuda' => 'required|string|max:100',
                'status_jual' => 'required|in:' . Kuda::STATUS_TERSEDIA . ',' . Kuda::STATUS_TERJUAL . ',' . Kuda::STATUS_BREEDING,
                'harga_buka' => 'required|numeric|min:0',
                'id_peternakan' => 'required|exists:peternakan,id_peternakan',
                'id_ibu' => 'nullable|exists:kuda,id_kuda',
                'id_ayah' => 'nullable|exists:kuda,id_kuda',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        $kuda = Kuda::create($validated);

        return $this->successResponse(
            $kuda->load(['peternakan.user', 'lisensi']),
            'Kuda berhasil ditambahkan',
            201
        );
    }

    public function update(Request $request, $id)
    {
        $kuda = Kuda::find($id);

        if (!$kuda) {
            return $this->errorResponse('Kuda tidak ditemukan', 404);
        }

        try {
            $validated = $request->validate([
                'nama_kuda' => 'sometimes|required|string|max:100',
                'jenis_kuda' => 'sometimes|required|string|max:100',
                'status_jual' => ['sometimes', 'required', Rule::in([Kuda::STATUS_TERSEDIA, Kuda::STATUS_TERJUAL, Kuda::STATUS_BREEDING])],
                'harga_buka' => 'sometimes|required|numeric|min:0',
                'id_peternakan' => 'sometimes|required|exists:peternakan,id_peternakan',
                'id_ibu' => 'nullable|exists:kuda,id_kuda',
                'id_ayah' => 'nullable|exists:kuda,id_kuda',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        $kuda->update($validated);

        return $this->successResponse(
            $kuda->load(['peternakan.user', 'lisensi', 'ibu', 'ayah']),
            'Kuda berhasil diperbarui'
        );
    }

    public function destroy($id)
    {
        $kuda = Kuda::find($id);

        if (!$kuda) {
            return $this->errorResponse('Kuda tidak ditemukan', 404);
        }

        $kuda->delete();

        return $this->successResponse(null, 'Kuda berhasil dihapus');
    }
}
