<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        // Mengambil data user dengan pagination
        $users = User::latest()->paginate(10);

        // Mengembalikan response data user
        return $this->successResponse($users, 'Data user berhasil diambil');
    }

    public function show($id)
    {
        // Mengambil detail user berdasarkan ID
        $user = User::find($id);

        // Mengembalikan error jika user tidak ditemukan
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        // Mengembalikan response detail user
        return $this->successResponse($user, 'Detail user berhasil diambil');
    }

    public function store(Request $request)
    {
        try {
            // Memvalidasi data user sebelum disimpan
            $validated = $this->validateUserData($request);
        } catch (ValidationException $e) {
            // Mengembalikan error jika validasi gagal
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        // Melakukan hash password sebelum disimpan
        $validated['password'] = Hash::make($validated['password']);

        // Menyimpan data user baru
        $user = User::create($validated);

        // Mengembalikan response user berhasil ditambahkan
        return $this->successResponse($user, 'User berhasil ditambahkan', 201);
    }

    public function update(Request $request, $id)
    {
        // Mengambil data user yang akan diperbarui
        $user = User::find($id);

        // Mengembalikan error jika user tidak ditemukan
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        try {
            // Memvalidasi data user yang akan diperbarui
            $validated = $this->validateUserUpdateData($request, $user);
        } catch (ValidationException $e) {
            // Mengembalikan error jika validasi gagal
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        // Melakukan hash password jika password baru dikirim
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Memperbarui data user
        $user->update($validated);

        // Mengembalikan response user berhasil diperbarui
        return $this->successResponse($user, 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        // Mengambil data user yang akan dihapus
        $user = User::find($id);

        // Mengembalikan error jika user tidak ditemukan
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        // Menghapus data user
        $user->delete();

        // Mengembalikan response user berhasil dihapus
        return $this->successResponse(null, 'User berhasil dihapus');
    }

    private function validateUserData(Request $request)
    {
        // Validasi untuk menambah data user
        return $request->validate([
            'nama_lengkap' => 'required|string|max:60',
            'email' => 'required|email|unique:users,email',
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'role' => 'required|in:admin,pembeli,peternak',
            'password' => 'required|string|min:8',
        ]);
    }

    private function validateUserUpdateData(Request $request, User $user)
    {
        // Validasi untuk memperbarui data user
        return $request->validate([
            'nama_lengkap' => 'sometimes|required|string|max:60',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id_user, 'id_user'),
            ],
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'role' => 'sometimes|required|in:admin,pembeli,peternak',
            'password' => 'nullable|string|min:8',
        ]);
    }
}
