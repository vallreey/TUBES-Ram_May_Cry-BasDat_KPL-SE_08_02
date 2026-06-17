<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\SecuresKudaSearchInput;
use App\Models\Kuda;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KudaApiController extends Controller
{
    use ApiResponse, SecuresKudaSearchInput;

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

        // Filter data kuda berdasarkan gender
        if ($request->filled('gender') && in_array($request->gender, [Kuda::GENDER_JANTAN, Kuda::GENDER_BETINA], true)) {
            $query->where('gender', $request->gender);
        }

        // Mencari data kuda berdasarkan nama, jenis, atau peternakan.
        // Keyword diamankan dengan escaping LIKE dan parameter binding.
        $search = $this->normalizeKudaSearchKeyword($request->input('search'));

        if ($search !== null) {
            $keyword = $this->makeSecureLikeKeyword($search);

            $query->where(function ($q) use ($keyword) {
                $q->whereRaw($this->secureLikeSql('nama_kuda'), [$keyword])
                  ->orWhereRaw($this->secureLikeSql('jenis_kuda'), [$keyword])
                  ->orWhereHas('peternakan', function ($peternakanQuery) use ($keyword) {
                      $peternakanQuery->whereRaw(
                          $this->secureLikeSql('nama_peternakan'),
                          [$keyword]
                      );
                  });
            });
        }

        // Sorting data kuda
        switch ($request->input('sort', 'terbaru')) {
            case 'nama_asc':
                $query->orderBy('nama_kuda', 'asc');
                break;

            case 'nama_desc':
                $query->orderBy('nama_kuda', 'desc');
                break;

            case 'terlama':
                $query->orderBy('created_at', 'asc');
                break;

            case 'terbaru':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Mengambil data kuda dengan pagination
        $kuda = $query->paginate(10)->withQueryString();

        // Mengembalikan response data kuda
        return $this->successResponse($kuda, 'Data kuda berhasil diambil');
    }

    public function show($id)
    {
        // Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif

        // Mengambil detail kuda berdasarkan ID
        $kuda = Kuda::with(['peternakan.user', 'lisensi', 'ibu', 'ayah', 'transaksi'])
            ->find($id);

        // staging

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
            // Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif

            // Memvalidasi data kuda sebelum disimpan
            $validated = $this->validateKudaData($request);

            // staging
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
            // Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif

            // Memvalidasi data kuda yang akan diperbarui
            $validated = $this->validateKudaUpdateData($request, $kuda);

            // staging
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
        $idPeternakan = (int) $request->input('id_peternakan');

        // Validasi untuk menambah data kuda
        return $request->validate([
            'nama_kuda' => 'required|string|max:100',
            'jenis_kuda' => 'required|string|max:100',
            'gender' => [
                'required',
                Rule::in([
                    Kuda::GENDER_JANTAN,
                    Kuda::GENDER_BETINA,
                ]),
            ],
            'status_jual' => [
                'required',
                Rule::in([
                    Kuda::STATUS_TERSEDIA,
                    Kuda::STATUS_TERJUAL,
                    Kuda::STATUS_BREEDING,
                ]),
            ],
            'harga_buka' => 'required|numeric|min:0',
            'id_peternakan' => 'required|exists:peternakan,id_peternakan',
            'id_ibu' => $this->getIndukRules($idPeternakan, Kuda::GENDER_BETINA),
            'id_ayah' => $this->getIndukRules($idPeternakan, Kuda::GENDER_JANTAN),
        ], $this->getIndukValidationMessages());
    }

    private function validateKudaUpdateData(Request $request, Kuda $kuda)
    {
        $idPeternakan = (int) $request->input('id_peternakan', $kuda->id_peternakan);

        // Validasi untuk memperbarui data kuda
        return $request->validate([
            'nama_kuda' => 'sometimes|required|string|max:100',
            'jenis_kuda' => 'sometimes|required|string|max:100',
            'gender' => [
                'sometimes',
                'required',
                Rule::in([
                    Kuda::GENDER_JANTAN,
                    Kuda::GENDER_BETINA,
                ]),
            ],
            'status_jual' => [
                'sometimes',
                'required',
                Rule::in([
                    Kuda::STATUS_TERSEDIA,
                    Kuda::STATUS_TERJUAL,
                    Kuda::STATUS_BREEDING,
                ]),
            ],
            'harga_buka' => 'sometimes|required|numeric|min:0',
            'id_peternakan' => 'sometimes|required|exists:peternakan,id_peternakan',
            'id_ibu' => $this->getIndukRules($idPeternakan, Kuda::GENDER_BETINA, $kuda->id_kuda),
            'id_ayah' => $this->getIndukRules($idPeternakan, Kuda::GENDER_JANTAN, $kuda->id_kuda),
        ], $this->getIndukValidationMessages());
    }

    private function getIndukRules(int $idPeternakan, string $gender, ?int $exceptIdKuda = null): array
    {
        $rules = [
            'nullable',
            Rule::exists('kuda', 'id_kuda')->where(function ($query) use ($idPeternakan, $gender) {
                return $query
                    ->where('id_peternakan', $idPeternakan)
                    ->where('gender', $gender);
            }),
        ];

        if ($exceptIdKuda) {
            $rules[] = Rule::notIn([$exceptIdKuda]);
        }

        return $rules;
    }

    private function getIndukValidationMessages(): array
    {
        return [
            'id_ibu.exists'  => 'Ibu harus kuda betina dari peternakan sendiri.',
            'id_ayah.exists' => 'Ayah harus kuda jantan dari peternakan sendiri.',
            'id_ibu.not_in'  => 'Kuda tidak bisa menjadi ibu untuk dirinya sendiri.',
            'id_ayah.not_in' => 'Kuda tidak bisa menjadi ayah untuk dirinya sendiri.',
        ];
    }
}
