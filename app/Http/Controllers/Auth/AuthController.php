<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * ------------------------------------------------------------
     *  Dependency Injection
     * ------------------------------------------------------------
     *  FonnteService digunakan untuk pengiriman OTP via WhatsApp
     */
    protected FonnteService $fonnte;

    public function __construct(FonnteService $fonnte)
    {
        $this->fonnte = $fonnte;
    }

    /**
     * ============================================================
     *  REGISTER
     * ============================================================
     */

    /**
     * Menampilkan halaman registrasi
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi user + pengiriman OTP aktivasi
     */
    public function register(Request $request)
    {
        /**
         * --------------------------------------------------------
         *  Validasi Input Registrasi
         * --------------------------------------------------------
         */
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'regex:/^[A-Za-zÀ-ž\s\'\.\-]+$/'],
            'username' => ['required', 'string', 'min:4', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username'],
            'email' => ['required', 'email:rfc,dns', 'max:191', 'unique:users,email'],
            'phone' => ['required', 'numeric', 'digits_between:10,15'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:191',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/'
            ],
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi untuk verifikasi.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
        ]);

        try {
            DB::beginTransaction();

            /**
             * ----------------------------------------------------
             *  Create User & Profile
             * ----------------------------------------------------
             */
            $user = User::create([
                'name'        => $validated['name'],
                'username'    => $validated['username'],
                'email'       => $validated['email'],
                'password'    => Hash::make($validated['password']),
                'role'        => 'buyer',
                'status'      => 'active',
                'is_verified' => false,
                'photo_url'   => null,
            ]);

            Profile::create([
                'user_id' => $user->user_id,
                'phone'   => $validated['phone'],
                'bio'     => 'Pengguna baru Telu Consign',
            ]);

            /**
             * ----------------------------------------------------
             *  Generate & Send OTP Activation
             * ----------------------------------------------------
             */
            $otp = $user->generateOtp();
            $message = "Halo {$user->name}, Kode OTP Aktivasi Tel-U Consign Anda adalah: *{$otp}*.";
            $this->fonnte->sendMessage($validated['phone'], $message);

            DB::commit();

            /**
             * ----------------------------------------------------
             *  Store OTP Context to Session
             * ----------------------------------------------------
             */
            session([
                'otp_user_id' => $user->user_id,
                'otp_context' => 'activation',
            ]);

            return redirect()
                ->route('otp.verify')
                ->with('success', 'Registrasi berhasil! Masukkan kode OTP yang dikirim ke WhatsApp.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     *  LOGIN + OTP (2FA)
     * ============================================================
     */

    /**
     * Menampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login menggunakan email / username + OTP
     */
    public function login(Request $request)
    {
        /**
         * --------------------------------------------------------
         *  Validasi Input Login
         * --------------------------------------------------------
         */
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        /**
         * --------------------------------------------------------
         *  Tentukan Login Type (Email / Username)
         * --------------------------------------------------------
         */
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $user = User::where($loginType, $request->login)->first();

        /**
         * --------------------------------------------------------
         *  Validasi User & Password
         * --------------------------------------------------------
         */
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['login' => 'Username/Email atau kata sandi salah.'])
                ->withInput();
        }

        /**
         * --------------------------------------------------------
         *  Validasi Status Akun
         * --------------------------------------------------------
         */
        if ($user->status !== 'active') {
            return back()
                ->withErrors(['login' => 'Akun Anda sedang ditangguhkan/non-aktif.'])
                ->withInput();
        }

        /**
         * --------------------------------------------------------
         *  Generate & Send OTP Login
         * --------------------------------------------------------
         */
        $otp = $user->generateOtp();
        $userPhone = $user->profile->phone ?? null;

        if ($userPhone) {
            $this->fonnte->sendMessage(
                $userPhone,
                "Kode OTP Login Tel-U Consign: *{$otp}*"
            );
        }

        /**
         * --------------------------------------------------------
         *  Store OTP Login Context
         * --------------------------------------------------------
         */
        session([
            'otp_user_id'  => $user->user_id,
            'otp_context'  => 'login',
            'otp_remember' => $request->has('remember'),
        ]);

        return redirect()
            ->route('otp.verify')
            ->with('info', 'Masukkan kode OTP untuk masuk.');
    }

    /**
     * ============================================================
     *  LOGOUT
     * ============================================================
     */

    /**
     * Logout user & destroy session
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda telah keluar.');
    }
}
