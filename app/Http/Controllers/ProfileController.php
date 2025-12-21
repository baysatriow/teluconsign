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
     * Memperbarui data diri (Nama, Bio, Telepon).
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:255',
        ]);

        $user = User::find(Auth::id());
        $user->update(['name' => $request->name]);

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

        // Logika Default Address
        $isDefault = !$hasAddress ? true : ($request->has('is_default') && $request->is_default == '1');

        if ($isDefault && $hasAddress) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        Address::create([
            'user_id' => Auth::id(),
            'label' => $request->label,
            'recipient' => $request->recipient,
            'phone' => $request->phone,
            'province' => $request->province_name,
            'city' => $request->city_name,
            'district' => $request->district_name,
            'village' => $request->village_name,
            'postal_code' => $request->postal_code,
            'detail_address' => $request->detail_address,
            'location_id' => $request->location_id, // Added
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

        // Validasi sebagian input (karena dropdown mungkin kosong jika tidak diubah)
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
        if ($request->filled('province_name')) {
            $dataToUpdate['province'] = $request->province_name;
            $dataToUpdate['city'] = $request->city_name;
            $dataToUpdate['district'] = $request->district_name;
            $dataToUpdate['village'] = $request->village_name;
            $dataToUpdate['location_id'] = $request->location_id; // Added
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

    /**
     * Menambahkan rekening bank.
     */
    public function addBank(Request $request)
    {
        $this->validateBank($request);

        BankAccount::create([
            'user_id' => Auth::id(),
            'bank_name' => $request->bank_name,
            'account_no' => $request->account_no,
            'account_name' => $request->account_name,
            'is_default' => true
        ]);

        return back()->with('success', 'Rekening Bank berhasil ditambahkan.');
    }

    /**
     * Memperbarui rekening bank.
     */
    public function updateBank(Request $request, $id)
    {
        $bank = BankAccount::where('user_id', Auth::id())->where('bank_account_id', $id)->first();

        if (!$bank) {
            return back()->with('error', 'Rekening tidak ditemukan.');
        }

        $this->validateBank($request);

        $bank->update([
            'bank_name' => $request->bank_name,
            'account_no' => $request->account_no,
            'account_name' => $request->account_name,
        ]);

        return back()->with('success', 'Rekening Bank berhasil diperbarui.');
    }

    /**
     * Menghapus rekening bank.
     */
    public function deleteBank($id)
    {
        $bank = BankAccount::where('user_id', Auth::id())->where('bank_account_id', $id)->first();

        if ($bank) {
            $bank->delete();
            return back()->with('success', 'Rekening berhasil dihapus.');
        }

        return back()->with('error', 'Rekening tidak ditemukan.');
    }

    // --- Helper Validation ---

    private function validateAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'recipient' => 'required|string',
            'phone' => 'required|numeric',
            'province_name' => 'required',
            'city_name' => 'required',
            'district_name' => 'required',
            // 'village_name' => 'required', // Optional depending on API
            'postal_code' => 'required|numeric',
            'detail_address' => 'required|string',
            // location_id is optional but recommended
        ]);
    }

    private function validateBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_no' => 'required|numeric',
            'account_name' => 'required|string'
        ]);
    }
}
