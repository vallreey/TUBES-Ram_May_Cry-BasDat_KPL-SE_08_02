<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Middleware helper: pastikan hanya admin yang bisa akses
    private function adminOnly()
    {
        if (auth()->user()->role !== User::ROLE_ADMIN) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function index()
    {
        $this->adminOnly();

        $users = User::with('peternakan')->latest()->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->adminOnly();

        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $this->adminOnly();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:50',
            'email'        => 'required|email|max:100|unique:users,email',
            'no_telp'      => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
            'role'         => 'required|in:admin,peternak,pembeli',
            'password'     => 'required|string|min:8|confirmed',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.unique'          => 'Email sudah digunakan.',
            'role.required'         => 'Role wajib dipilih.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email'        => $validated['email'],
            'no_telp'      => $validated['no_telp'] ?? null,
            'alamat'       => $validated['alamat'] ?? null,
            'role'         => $validated['role'],
            'password'     => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function show($id)
    {
        $this->adminOnly();

        $user = User::with(['peternakan'])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $this->adminOnly();

        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $this->adminOnly();

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:50',
            'email'        => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'no_telp'      => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
            'role'         => 'required|in:admin,peternak,pembeli',
            'password'     => 'nullable|string|min:8|confirmed',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.unique'          => 'Email sudah digunakan akun lain.',
            'role.required'         => 'Role wajib dipilih.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        // Mencegah admin mengubah role dirinya sendiri
        if ($user->id_user === auth()->user()->id_user && $validated['role'] !== User::ROLE_ADMIN) {
            return redirect()->back()
                ->with('error', 'Anda tidak bisa mengubah role akun Anda sendiri.')
                ->withInput();
        }

        $updateData = [
            'nama_lengkap' => $validated['nama_lengkap'],
            'email'        => $validated['email'],
            'no_telp'      => $validated['no_telp'] ?? null,
            'alamat'       => $validated['alamat'] ?? null,
            'role'         => $validated['role'],
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()
            ->route('users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->adminOnly();

        $user = User::findOrFail($id);

        if ($user->id_user === auth()->user()->id_user) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        // Cek apakah user punya relasi yang mencegah penghapusan
        $punyaTransaksi = \App\Models\Transaksi::where('id_pembeli', $user->id_user)
            ->orWhere('id_penjual', $user->id_user)
            ->exists();

        $punyaPeternakan = \App\Models\Peternakan::where('id_user', $user->id_user)->exists();

        if ($punyaTransaksi || $punyaPeternakan) {
            return redirect()->route('users.index')
                ->with('error', 'User tidak bisa dihapus karena masih memiliki data transaksi atau peternakan terkait.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function profile()
    {
        return view('admin.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:60'],
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'no_telp'      => ['nullable', 'string', 'max:15'],
            'alamat'       => ['nullable', 'string'],
            'password_lama' => ['nullable', 'required_with:password'],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'nama_lengkap.required'       => 'Username / nama lengkap wajib diisi.',
            'nama_lengkap.max'            => 'Username / nama lengkap maksimal 60 karakter.',
            'email.required'              => 'Email wajib diisi.',
            'email.email'                 => 'Format email tidak valid.',
            'email.unique'               => 'Email sudah digunakan oleh akun lain.',
            'password_lama.required_with' => 'Password lama wajib diisi jika ingin mengganti password.',
            'password.min'               => 'Password baru minimal 8 karakter.',
            'password.confirmed'         => 'Konfirmasi password baru tidak cocok.',
        ]);

        if ($request->filled('password') && !Hash::check($request->password_lama, $user->password)) {
            return back()
                ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        $user->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email'        => $validated['email'],
            'no_telp'      => $validated['no_telp'] ?? null,
            'alamat'       => $validated['alamat'] ?? null,
            'password'     => $request->filled('password')
                ? Hash::make($validated['password'])
                : $user->password,
        ]);

        return redirect()
            ->route('profile')
            ->with('success', 'Profile berhasil diperbarui.');
    }
}
