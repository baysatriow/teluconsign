<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // GET /register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // POST /register
    public function register(Request $request)
    {
        // validasi register
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:120'],
                'username' => ['required', 'string', 'max:50', 'alpha_num', 'unique:users,username'],
                'email' => ['required', 'email', 'max:191', 'unique:users,email'],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed', // butuh field password_confirmation
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/'
                ],
            ],
            [
                'name.required' => 'Nama wajib diisi.',
                'username.required' => 'Username wajib diisi.',
                'username.alpha_num' => 'Username hanya huruf/angka.',
                'username.unique' => 'Username sudah dipakai.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar.',
                'password.required' => 'Kata sandi wajib diisi.',
                'password.min' => 'Minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi tidak sama.',
                'password.regex' => 'Harus ada huruf besar, kecil, angka, dan simbol.',
            ]
        );

        // buat user
        $user = User::create([
            'role'          => 'buyer',
            'status'        => 'active',
            'username'      => $validated['username'],
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'photo_url'     => null,
        ]);

        // login otomatis
        Auth::login($user);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Akun berhasil dibuat! Selamat datang 👋');
    }

    // GET /login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // POST /login
    public function login(Request $request)
    {
        // validasi login
        $credentials = $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Kata sandi wajib diisi.',
            ]
        );

        // attempt login + status aktif
        $ok = Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'status' => 'active',
        ]);

        if (!$ok) {
            return back()
                ->withErrors(['email' => 'Email / kata sandi salah atau akun tidak aktif.'])
                ->withInput();
        }

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Login berhasil 👋');
    }

    // POST /logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login.form')
            ->with('success', 'Kamu sudah logout.');
    }
}
