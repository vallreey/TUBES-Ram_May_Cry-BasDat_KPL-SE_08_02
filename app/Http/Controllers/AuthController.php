<?php

namespace App\Http\Controllers;

use App\Models\Peternakan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan form login
    public function loginForm()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    /**
     * Tampilkan halaman pilihan role, atau langsung form register
     * jika ?role=pembeli / ?role=peternak sudah ada di query string.
     */
    public function registerForm(Request $request)
    {
        if (Auth::check()) return redirect()->route('dashboard');

        $role = $request->query('role');

        if ($role === User::ROLE_PEMBELI) {
            return view('auth.register-pembeli');
        }

        if ($role === User::ROLE_PETERNAK) {
            return view('auth.register-peternak');
        }

        // Belum pilih role → tampilkan halaman pilihan
        return view('auth.register-role');
    }

    // Proses register  
    public function register(Request $request)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:60',
            'email'        => 'required|email|unique:users,email',
            'no_telp'      => 'nullable|string|max:15',
            'alamat'       => 'nullable|string',
            // Parameterisasi di validasi
            'role'         => 'required|in:' . User::ROLE_PEMBELI . ',' . User::ROLE_PETERNAK,
            'password'     => 'required|min:8|confirmed',
        ];

        $messages = [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.unique'          => 'Email sudah terdaftar.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'role.required'         => 'Role wajib dipilih.',
        ];

        // Tambah validasi data peternakan jika role peternak
        if ($request->input('role') === User::ROLE_PETERNAK) {
            $rules['nama_peternakan']  = 'required|string|max:100';
            $rules['kapasitas_kandang'] = 'required|integer|min:0';
            $rules['lokasi_map']       = 'nullable|string|max:255';
            $rules['alamat_lengkap']   = 'nullable|string';

            $messages['nama_peternakan.required']   = 'Nama peternakan wajib diisi.';
            $messages['kapasitas_kandang.required']  = 'Kapasitas kandang wajib diisi.';
            $messages['kapasitas_kandang.integer']   = 'Kapasitas kandang harus berupa angka.';
        }

        $request->validate($rules, $messages);

        // Buat user
        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'no_telp'      => $request->no_telp,
            'alamat'       => $request->alamat,
            'role'         => $request->role,
            'password'     => Hash::make($request->password),
        ]);

        // Jika peternak, buat data peternakan sekaligus
        if ($request->role === User::ROLE_PETERNAK) {
            Peternakan::create([
                'nama_peternakan'  => $request->nama_peternakan,
                'kapasitas_kandang' => $request->kapasitas_kandang,
                'lokasi_map'       => $request->lokasi_map,
                'alamat_lengkap'   => $request->alamat_lengkap,
                'id_user'          => $user->id_user,
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function testApi()
    {
        return response()->json([
            'message' => 'API berhasil jalan'
        ]);
    }
}