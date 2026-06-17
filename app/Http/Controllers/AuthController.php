<?php

namespace App\Http\Controllers;

use App\Models\Peternakan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        // Mengarahkan user yang sudah login ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Menampilkan halaman login
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Memvalidasi input login
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Mengambil data login dari request
        $credentials = [
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ];

        // Mengambil status remember me
        $remember = $request->boolean('remember');

        // Mengecek kecocokan email dan password
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        // Mengembalikan pesan error jika login gagal
        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput();
    }

    public function registerForm(Request $request)
    {
        // Mengarahkan user yang sudah login ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Mengambil role dari query string
        $role = $request->query('role');

        // Parameterization/Generics-AdhiPuspoHadikusumo

        // Menampilkan form register pembeli
        if ($role === User::ROLE_PEMBELI) {
            return view('auth.register-pembeli');
        }

        // Menampilkan form register peternak
        if ($role === User::ROLE_PETERNAK) {
            return view('auth.register-peternak');
        }

        // Menampilkan halaman pilih role
        return view('auth.register-role');
    }

    public function register(Request $request)
    {
        // Parameterization/Generics-AdhiPuspoHadikusumo

        // Memvalidasi data registrasi
        $validated = $this->validateRegisterData($request);

        // staging

        // Membuat akun user baru
        $user = User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email'        => $validated['email'],
            'no_telp'      => $validated['no_telp'] ?? null,
            'alamat'       => $validated['alamat'] ?? null,
            'role'         => $validated['role'],
            'password'     => Hash::make($validated['password']),
        ]);

        // Parameterization/Generics-AdhiPuspoHadikusumo-MuhammadNaufalHanif

        // Membuat data peternakan jika user mendaftar sebagai peternak
        if ($validated['role'] === User::ROLE_PETERNAK) {
            // staging

            Peternakan::create([
                'nama_peternakan'   => $validated['nama_peternakan'],
                'kapasitas_kandang' => $validated['kapasitas_kandang'],
                'lokasi_map'        => $validated['lokasi_map'] ?? null,
                'alamat_lengkap'    => $validated['alamat_lengkap'] ?? null,
                'id_user'           => $user->id_user,
            ]);
        }

        // Login otomatis setelah registrasi berhasil
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        // Menghapus session login user
        Auth::logout();

        // Menghapus session lama
        $request->session()->invalidate();

        // Membuat token session baru
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function testApi()
    {
        // Mengecek apakah route API berhasil dijalankan
        return response()->json([
            'message' => 'API berhasil jalan',
        ]);
    }

    private function validateRegisterData(Request $request)
    {
        // Aturan validasi dasar untuk register
        $rules = [
            'nama_lengkap' => 'required|string|max:60',
            'email'        => 'required|email|unique:users,email',
            'no_telp'      => 'nullable|string|max:15',
            'alamat'       => 'nullable|string',
            'role'         => 'required|in:' . User::ROLE_PEMBELI . ',' . User::ROLE_PETERNAK,
            'password'     => 'required|min:8|confirmed',
        ];

        // Pesan error validasi register
        $messages = [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah terdaftar.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'role.required'         => 'Role wajib dipilih.',
            'role.in'               => 'Role tidak valid.',
        ];

        // Menambahkan validasi khusus jika role adalah peternak
        if ($request->input('role') === User::ROLE_PETERNAK) {
            $rules['nama_peternakan']   = 'required|string|max:100';
            $rules['kapasitas_kandang'] = 'required|integer|min:0';
            $rules['lokasi_map']        = 'nullable|string|max:255';
            $rules['alamat_lengkap']    = 'nullable|string';

            $messages['nama_peternakan.required']   = 'Nama peternakan wajib diisi.';
            $messages['kapasitas_kandang.required'] = 'Kapasitas kandang wajib diisi.';
            $messages['kapasitas_kandang.integer']  = 'Kapasitas kandang harus berupa angka.';
            $messages['kapasitas_kandang.min']      = 'Kapasitas kandang tidak boleh negatif.';
        }

        // Mengembalikan data yang sudah tervalidasi
        return $request->validate($rules, $messages);
    }
}
