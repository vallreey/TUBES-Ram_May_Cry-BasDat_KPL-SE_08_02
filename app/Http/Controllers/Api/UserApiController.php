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
        $users = User::latest()->paginate(10);
        return $this->successResponse($users, 'Data user berhasil diambil');
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        return $this->successResponse($user, 'Detail user berhasil diambil');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_lengkap' => 'required|string|max:60',
                'email' => 'required|email|unique:users,email',
                'no_telp' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'role' => 'required|in:admin,pembeli,peternak',
                'password' => 'required|string|min:8',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return $this->successResponse($user, 'User berhasil ditambahkan', 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        try {
            $validated = $request->validate([
                'nama_lengkap' => 'sometimes|required|string|max:60',
                'email' => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
                'no_telp' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'role' => 'sometimes|required|in:admin,pembeli,peternak',
                'password' => 'nullable|string|min:8',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return $this->successResponse($user, 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        $user->delete();

        return $this->successResponse(null, 'User berhasil dihapus');
    }
}
