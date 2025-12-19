<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\FonnteService;

class ForgotPasswordController extends Controller
{
    protected $fonnte;

    public function __construct(FonnteService $fonnte)
    {
        $this->fonnte = $fonnte;
    }

    // --- LANGKAH 1: FORM CARI AKUN ---
    public function showSearchForm()
    {
        return view('auth.forgot-password.search');
    }

    public function search(Request $request)
    {
        $request->validate([
            'credential' => 'required|string', // Bisa email atau username
        ]);

        $credential = $request->credential;

        $user = User::where('email', $credential)
                    ->orWhere('username', $credential)
                    ->first();

        if (!$user) {
            return back()->with('error', 'Akun tidak ditemukan.');
        }

        // Cek apakah user punya nomor HP di profile
        $phone = $user->profile->phone ?? null;
        if (!$phone) {
            return back()->with('error', 'Akun ini tidak memiliki nomor telepon terdaftar. Hubungi admin.');
        }

        // Simpan user_id di session sementara untuk langkah selanjutnya
        session(['reset_user_id' => $user->user_id]);

        return redirect()->route('password.verify.show');
    }

    // --- LANGKAH 2: VERIFIKASI NOMOR HP ---
    public function showVerifyForm()
    {
        if (!session('reset_user_id')) {
            return redirect()->route('password.request');
        }

        $user = User::find(session('reset_user_id'));
        if (!$user) return redirect()->route('password.request');

        $phone = $user->profile->phone;

        // Sensor Nomor HP (081234567890 -> 0812*****890)
        $length = strlen($phone);
        $visibleStart = 4;
        $visibleEnd = 3;
        $maskedPhone = substr($phone, 0, $visibleStart) . str_repeat('*', $length - ($visibleStart + $visibleEnd)) . substr($phone, -$visibleEnd);

        return view('auth.forgot-password.verify', compact('maskedPhone'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        $userId = session('reset_user_id');
        if (!$userId) return redirect()->route('password.request');

        $user = User::find($userId);
        $savedPhone = $user->profile->phone;

        // Cek kecocokan nomor (Normalisasi dulu jika perlu, tapi disini kita asumsi input user harus persis)
        // Kita bisa buat agak longgar misal 08 vs 628, tapi untuk keamanan ketat, harus persis.
        // Mari kita buat agar 08... dan 628... dianggap sama.
        $inputPhone = $this->normalizePhone($request->phone);
        $dbPhone = $this->normalizePhone($savedPhone);

        if ($inputPhone !== $dbPhone) {
            return back()->with('error', 'Nomor telepon tidak cocok dengan data kami.');
        }

        // --- GENERATE LINK RESET ---
        $token = Str::random(64);

        // Simpan token ke tabel password_reset_tokens
        // Hapus token lama user ini dulu
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token, // Token polosan (biasanya di-hash, tapi untuk link sekali pakai ini ok asal ada expired)
            'created_at' => Carbon::now()
        ]);

        // Buat Link
        $link = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // Kirim Link via WA (Fonnte)
        $message = "Halo {$user->name},\n\nKami menerima permintaan reset password untuk akun Anda.\nKlik link berikut untuk membuat password baru:\n\n{$link}\n\nLink ini hanya berlaku selama 60 menit dan hanya bisa digunakan 1 kali.\n\nJika Anda tidak meminta ini, abaikan pesan ini.";

        $this->fonnte->sendMessage($savedPhone, $message);

        // Hapus session user id agar tidak bisa back
        session()->forget('reset_user_id');

        return redirect()->route('login')->with('success', 'Link reset password telah dikirim ke WhatsApp Anda. Silakan cek.');
    }

    // --- LANGKAH 3: FORM RESET PASSWORD (VIA LINK) ---
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.forgot-password.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/'
            ],
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, kecil, angka, dan simbol.',
        ]);

        // Cek Token di Database
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        // Validasi Token
        if (!$record) {
            return back()->with('error', 'Link reset password tidak valid atau salah.');
        }

        // Cek Expired (60 Menit)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete(); // Hapus karena expired
            return back()->with('error', 'Link reset password sudah kadaluarsa. Silakan minta ulang.');
        }

        // Update Password User
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus Token (Single Use)
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan login dengan password baru.');
    }

    private function normalizePhone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 2) == '08') return '62' . substr($phone, 1);
        if (substr($phone, 0, 1) == '8') return '62' . $phone;
        return $phone;
    }
}
