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
        // Mengambil data kuda beserta relasinya
        $query = Kuda::with(['peternakan.user', 'lisensi', 'ibu', 'ayah']);

        // Filter data kuda berdasarkan status jual
        if ($request->filled('status_jual')) {
            $query->where('status_jual', $request->status_jual);
        }

        // Filter data kuda berdasarkan peternakan
        if ($request->filled('id_peternakan')) {
            $query->where('id_peternakan', $request->id_peternakan);
        }

        // Mencari data kuda berdasarkan nama atau jenis
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_kuda', 'like', "%{$search}%")
                  ->orWhere('jenis_kuda', 'like', "%{$search}%");
            });
        }

        // Mengambil data kuda dengan pagination
        $kuda = $query->latest()->paginate(10);

        // Mengembalikan response data kuda
        return $this->successResponse($kuda, 'Data kuda berhasil diambil');
    }

    public function show($id)
    {
        // Mengambil detail kuda berdasarkan ID
        $kuda = Kuda::with(['peternakan.user', 'lisensi', 'ibu', 'ayah', 'transaksi'])
            ->find($id);

        // Mengembalikan error jika kuda tidak ditemukan
        if (!$kuda) {
            return $this->errorResponse('Kuda tidak ditemukan', 404);
        }

        // Mengembalikan response detail kuda
        return $this->successResponse($kuda, 'Detail kuda berhasil diambil');
    }

    public function store(Request $request)
    {
        try {
            // Memvalidasi data kuda sebelum disimpan
            $validated = $this->validateKudaData($request);
        } catch (ValidationException $e) {
            // Mengembalikan error jika validasi gagal
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        // Menyimpan data kuda baru
        $kuda = Kuda::create($validated);

        // Mengembalikan response kuda berhasil ditambahkan
        return $this->successResponse(
            $kuda->load(['peternakan.user', 'lisensi']),
            'Kuda berhasil ditambahkan',
            201
        );
    }

    public function update(Request $request, $id)
    {
        // Mengambil data kuda yang akan diperbarui
        $kuda = Kuda::find($id);

        // Mengembalikan error jika kuda tidak ditemukan
        if (!$kuda) {
            return $this->errorResponse('Kuda tidak ditemukan', 404);
        }

        try {
            // Memvalidasi data kuda yang akan diperbarui
            $validated = $this->validateKudaUpdateData($request);
        } catch (ValidationException $e) {
            // Mengembalikan error jika validasi gagal
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        // Memperbarui data kuda
        $kuda->update($validated);

        // Mengembalikan response kuda berhasil diperbarui
        return $this->successResponse(
            $kuda->load(['peternakan.user', 'lisensi', 'ibu', 'ayah']),
            'Kuda berhasil diperbarui'
        );
    }

    public function destroy($id)
    {
        // Mengambil data kuda yang akan dihapus
        $kuda = Kuda::find($id);

        // Mengembalikan error jika kuda tidak ditemukan
        if (!$kuda) {
            return $this->errorResponse('Kuda tidak ditemukan', 404);
        }

        // Menghapus data kuda
        $kuda->delete();

        // Mengembalikan response kuda berhasil dihapus
        return $this->successResponse(null, 'Kuda berhasil dihapus');
    }

    private function validateKudaData(Request $request)
    {
        // Validasi untuk menambah data kuda
        return $request->validate([
            'nama_kuda' => 'required|string|max:100',
            'jenis_kuda' => 'required|string|max:100',
            'status_jual' => 'required|in:tersedia,terjual,breeding',
            'harga_buka' => 'required|numeric|min:0',
            'id_peternakan' => 'required|exists:peternakan,id_peternakan',
            'id_ibu' => 'nullable|exists:kuda,id_kuda',
            'id_ayah' => 'nullable|exists:kuda,id_kuda',
        ]);
    }

    private function validateKudaUpdateData(Request $request)
    {
        // Validasi untuk memperbarui data kuda
        return $request->validate([
            'nama_kuda' => 'sometimes|required|string|max:100',
            'jenis_kuda' => 'sometimes|required|string|max:100',
            'status_jual' => ['sometimes', 'required', Rule::in(['tersedia', 'terjual', 'breeding'])],
            'harga_buka' => 'sometimes|required|numeric|min:0',
            'id_peternakan' => 'sometimes|required|exists:peternakan,id_peternakan',
            'id_ibu' => 'nullable|exists:kuda,id_kuda',
            'id_ayah' => 'nullable|exists:kuda,id_kuda',
        ]);
    }
}
