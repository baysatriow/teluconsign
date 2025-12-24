<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Address;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Services\FonnteService;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::with(['profile', 'addresses', 'bankAccounts'])->find(Auth::id());
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = User::find(Auth::id());
        
        $dataToUpdate = ['name' => $request->name];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $dataToUpdate['photo_url'] = asset('storage/' . $path);
        }

        $user->update($dataToUpdate);

        Profile::updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'phone' => $request->phone,
                'bio' => $request->bio
            ]
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
        ], [
            'new_password.regex' => 'Password harus mengandung huruf besar, kecil, angka, dan simbol.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        $user = User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    public function addAddress(Request $request)
    {
        $this->validateAddress($request);

        $hasAddress = Address::where('user_id', Auth::id())->exists();

        $isDefault = !$hasAddress ? true : ($request->has('is_default') && $request->is_default == '1');

        if ($isDefault && $hasAddress) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        Address::create([
            'user_id' => Auth::id(),
            'label' => $request->label,
            'recipient' => $request->recipient,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'village' => $request->village,
            'postal_code' => $request->postal_code,
            'detail_address' => $request->detail_address,
            'location_id' => $request->location_id, 
            'country' => 'ID',
            'is_default' => $isDefault
        ]);

        return back()->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    public function updateAddress(Request $request, $id)
    {
        $address = Address::where('user_id', Auth::id())->where('address_id', $id)->first();

        if (!$address) {
            return back()->with('error', 'Alamat tidak ditemukan.');
        }

        $request->validate([
            'label' => 'required|string|max:50',
            'recipient' => 'required|string',
            'phone' => 'required|numeric',
            'postal_code' => 'required|numeric',
            'detail_address' => 'required|string',
        ]);

        $dataToUpdate = [
            'label' => $request->label,
            'recipient' => $request->recipient,
            'phone' => $request->phone,
            'postal_code' => $request->postal_code,
            'detail_address' => $request->detail_address,
        ];

        if ($request->filled('province')) {
            $dataToUpdate['province'] = $request->province;
            $dataToUpdate['city'] = $request->city;
            $dataToUpdate['district'] = $request->district;
            $dataToUpdate['village'] = $request->village;
            $dataToUpdate['location_id'] = $request->location_id; 
        }

        if ($request->has('is_default') && $request->is_default == '1') {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
            $dataToUpdate['is_default'] = true;
        }

        $address->update($dataToUpdate);

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function deleteAddress($id)
    {
        $address = Address::where('user_id', Auth::id())->where('address_id', $id)->first();

        if ($address) {
            $wasDefault = $address->is_default;
            $address->delete();

            if ($wasDefault) {
                $newDefault = Address::where('user_id', Auth::id())->first();
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            return back()->with('success', 'Alamat berhasil dihapus.');
        }

        return back()->with('error', 'Alamat tidak ditemukan.');
    }

    public function setDefaultAddress($id)
    {
        Address::setDefault($id);
        return back()->with('success', 'Alamat utama berhasil diperbarui.');
    }

    private function validateAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'recipient' => 'required|string',
            'phone' => 'required|numeric',
            'province' => 'required',
            'city' => 'required',
            'district' => 'required',
            'postal_code' => 'required|numeric',
            'detail_address' => 'required|string',
        ]);
    }

    public function requestPhoneUpdate(Request $request, FonnteService $fonnte)
    {
        $request->validate([
            'new_phone' => 'required|numeric|digits_between:10,14'
        ]);

        $userId = Auth::id();
        $otp = rand(100000, 999999);
        $cacheKey = 'phone_update_' . $userId;

        Cache::put($cacheKey, [
            'new_phone' => $request->new_phone,
            'otp' => $otp
        ], now()->addMinutes(5));

        try {
            $fonnte->sendMessage(
                $request->new_phone, 
                "Kode Verifikasi Ganti Nomor: *{$otp}*\n\nJANGAN BERIKAN KODE INI KE SIAPAPUN."
            );
            return response()->json(['status' => 'success', 'message' => 'OTP dikirim ke nomor baru.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim OTP. Pastikan nomor WhatsApp aktif.'], 500);
        }
    }

    public function verifyPhoneUpdate(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6'
        ]);

        $userId = Auth::id();
        $cacheKey = 'phone_update_' . $userId;
        $tempData = Cache::get($cacheKey);

        if (!$tempData || $request->otp != $tempData['otp']) {
            return response()->json(['status' => 'error', 'message' => 'Kode OTP salah atau kadaluarsa.'], 400);
        }

        Profile::updateOrCreate(
            ['user_id' => $userId],
            ['phone' => $tempData['new_phone']]
        );

        Cache::forget($cacheKey);

        return response()->json(['status' => 'success', 'message' => 'Nomor WhatsApp berhasil diperbarui.']);
    }
}