<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Address;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil utama.
     */
    public function index()
    {
        $user = User::with(['profile', 'addresses', 'bankAccounts'])->find(Auth::id());
        return view('profile.index', compact('user'));
    }

    /**
     * Memperbarui data diri (Nama, Bio, Telepon, Foto).
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $user = User::find(Auth::id());
        
        $dataToUpdate = ['name' => $request->name];

        // Handle Photo Upload
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

    /**
     * Memperbarui kata sandi pengguna.
     */
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

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    /**
     * Menambahkan alamat baru.
     */
    public function addAddress(Request $request)
    {
        $this->validateAddress($request);

        // Cek apakah user sudah punya alamat
        $hasAddress = Address::where('user_id', Auth::id())->exists();

        // Logika Default Address (Jika belum punya alamat, otomatis default)
        $isDefault = !$hasAddress ? true : ($request->has('is_default') && $request->is_default == '1');

        if ($isDefault && $hasAddress) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        Address::create([
            'user_id' => Auth::id(),
            'label' => $request->label,
            'recipient' => $request->recipient,
            'phone' => $request->phone,
            'province' => $request->province,     // Corrected
            'city' => $request->city,             // Corrected
            'district' => $request->district,     // Corrected
            'village' => $request->village,       // Corrected
            'postal_code' => $request->postal_code,
            'detail_address' => $request->detail_address,
            'location_id' => $request->location_id, 
            'country' => 'ID',
            'is_default' => $isDefault
        ]);

        return back()->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui alamat yang sudah ada.
     */
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

        // Siapkan data update dasar
        $dataToUpdate = [
            'label' => $request->label,
            'recipient' => $request->recipient,
            'phone' => $request->phone,
            'postal_code' => $request->postal_code,
            'detail_address' => $request->detail_address,
        ];

        // Jika user memilih wilayah baru (dropdown tidak kosong), update juga wilayahnya
        if ($request->filled('province')) {
            $dataToUpdate['province'] = $request->province;
            $dataToUpdate['city'] = $request->city;
            $dataToUpdate['district'] = $request->district;
            $dataToUpdate['village'] = $request->village;
            $dataToUpdate['location_id'] = $request->location_id; 
        }

        // Handle Toggle Default saat Edit
        if ($request->has('is_default') && $request->is_default == '1') {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
            $dataToUpdate['is_default'] = true;
        }

        $address->update($dataToUpdate);

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * Menghapus alamat.
     */
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

    /**
     * Set alamat utama.
     */
    public function setDefaultAddress($id)
    {
        Address::setDefault($id);
        return back()->with('success', 'Alamat utama berhasil diperbarui.');
    }

    // --- Helper Validation ---

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
}
