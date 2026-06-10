<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peternakan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        try {
            // Memvalidasi data register API
            $validated = $request->validate([
                'nama_lengkap' => 'required|string|max:60',
                'email' => 'required|email|unique:users,email',
                'no_telp' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'role' => 'required|in:' . User::ROLE_ADMIN . ',' . User::ROLE_PEMBELI . ',' . User::ROLE_PETERNAK,
                'password' => 'required|min:8|confirmed',
                'nama_peternakan' => 'required_if:role,' . User::ROLE_PETERNAK . '|nullable|string|max:100',
                'kapasitas_kandang' => 'required_if:role,' . User::ROLE_PETERNAK . '|nullable|integer|min:0',
                'lokasi_map' => 'nullable|string|max:255',
                'alamat_lengkap' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            // Mengembalikan error jika validasi gagal
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        // Membuat akun user baru
        $user = User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        // Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif

        // Membuat data peternakan jika user mendaftar sebagai peternak
        if ($user->role === User::ROLE_PETERNAK) {
            // staging

            Peternakan::create([
                'nama_peternakan' => $validated['nama_peternakan'],
                'kapasitas_kandang' => $validated['kapasitas_kandang'],
                'lokasi_map' => $validated['lokasi_map'] ?? null,
                'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
                'id_user' => $user->id_user,
            ]);
        }

        // Mengembalikan response register berhasil
        return $this->successResponse(
            $user->load('peternakan'),
            'Registrasi API berhasil',
            201
        );
    }

    public function login(Request $request)
    {
        try {
            // Memvalidasi data login API
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            // Mengembalikan error jika validasi gagal
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        // Mencari user berdasarkan email
        $user = User::where('email', $validated['email'])->first();

        // Mengecek email dan password
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->errorResponse('Email atau password salah', 401);
        }

        // Mengembalikan response login berhasil
        return $this->successResponse($user, 'Login API berhasil');
    }
}
