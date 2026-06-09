<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Hanya admin yang bisa melihat data user
        if (auth()->user()->role !== 'admin') {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman user.');
        }

        // Mengambil semua data user
        $users = User::latest()->get();

        // Menampilkan halaman data user
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        // Mengarahkan ke halaman user karena fitur tambah user manual belum digunakan
        return redirect()
            ->route('users.index')
            ->with('error', 'Fitur tambah user manual belum tersedia.');
    }

    public function store(Request $request)
    {
        // Mengarahkan ke halaman user karena fitur simpan user manual belum digunakan
        return redirect()
            ->route('users.index')
            ->with('error', 'Fitur simpan user manual belum tersedia.');
    }

    public function show($id)
    {
        // Hanya admin yang bisa melihat detail user
        if (auth()->user()->role !== 'admin') {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke detail user.');
        }

        // Mengambil data user berdasarkan ID
        $user = User::findOrFail($id);

        // Menampilkan halaman detail user
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        // Mengarahkan ke halaman user karena fitur edit user manual belum digunakan
        return redirect()
            ->route('users.index')
            ->with('error', 'Fitur edit user manual belum tersedia.');
    }

    public function update(Request $request, $id)
    {
        // Mengarahkan ke halaman user karena fitur update user manual belum digunakan
        return redirect()
            ->route('users.index')
            ->with('error', 'Fitur update user manual belum tersedia.');
    }

    public function destroy($id)
    {
        // Hanya admin yang bisa menghapus user
        if (auth()->user()->role !== 'admin') {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus user.');
        }

        // Mengambil data user yang akan dihapus
        $user = User::findOrFail($id);

        // Mencegah admin menghapus akunnya sendiri
        if ($user->id_user === auth()->user()->id_user) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        // Menghapus data user
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function profile()
    {
        // Menampilkan halaman profile user yang sedang login
        return view('admin.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        // Mengambil user yang sedang login
        $user = Auth::user();

        // Memvalidasi data profile
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:60'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id_user, 'id_user'),
            ],
            'no_telp' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
            'password_lama' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'nama_lengkap.required' => 'Username / nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Username / nama lengkap maksimal 60 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password_lama.required_with' => 'Password lama wajib diisi jika ingin mengganti password.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Mengecek password lama jika user ingin mengganti password
        if ($request->filled('password') && !Hash::check($request->password_lama, $user->password)) {
            return back()
                ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        // Memperbarui data profile
        $user->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'password' => $request->filled('password')
                ? Hash::make($validated['password'])
                : $user->password,
        ]);

        return redirect()
            ->route('profile')
            ->with('success', 'Profile berhasil diperbarui.');
    }
}
