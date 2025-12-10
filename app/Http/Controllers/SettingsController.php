<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\BankAccount;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PROFILE PAGE
    |--------------------------------------------------------------------------
    */

    public function profile()
    {
        $user = Auth::user()->load('profile');
        return view('settings.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        /*
        |---------------------------------------------------------------
        | VALIDASI PROFIL
        |---------------------------------------------------------------
        */
        $request->validate([
            'name'  => ['required', 'string', 'max:120', 'regex:/^[a-zA-Z\s\.]+$/'],
            'email' => [
                'required',
                'email:dns',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')
            ],
            'phone' => ['nullable', 'regex:/^8[0-9]{7,12}$/'], // Tanpa +62
            'bio'   => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ], [
            'name.regex'  => 'Nama hanya boleh huruf, titik, dan spasi.',
            'phone.regex' => 'Nomor HP harus format Indonesia, contoh: 81234567890',
        ]);

        DB::beginTransaction();

        try {
            /*
            |---------------------------------------------------------------
            | HANDLE FOTO PROFIL (SIMPAN DI USERS)
            |---------------------------------------------------------------
            */
            $photoUrl = $user->photo_url;

            if ($request->hasFile('photo')) {

                // Hapus foto lama
                if ($photoUrl && Storage::disk('public')->exists($photoUrl)) {
                    Storage::disk('public')->delete($photoUrl);
                }

                // Upload baru
                $photoUrl = $request->file('photo')->store('profiles', 'public');
            }

            /*
            |---------------------------------------------------------------
            | UPDATE TABEL USERS
            |---------------------------------------------------------------
            */
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'photo_url' => $photoUrl
            ]);

            /*
            |---------------------------------------------------------------
            | UPDATE TABEL PROFILE (RELATION)
            |---------------------------------------------------------------
            */
            Profile::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'phone' => $request->phone,
                    'bio'   => $request->bio,
                ]
            );

            DB::commit();
            return back()->with('success', 'Profil berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | API: CHECK CURRENT PASSWORD
    |--------------------------------------------------------------------------
    */
    public function checkPassword(Request $request)
    {
        return Hash::check($request->current_password, Auth::user()->password_hash)
            ? response()->json(['status' => 'valid'])
            : response()->json(['status' => 'invalid']);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD
    |--------------------------------------------------------------------------
    */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers()
            ],
        ]);

        $user = Auth::user();

        // MANUAL CHECK — karena kolom bukan "password"
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors([
                'current_password' => 'Password lama tidak sesuai.'
            ]);
        }

        // Update password
        $user->update([
            'password_hash' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    /*
    |--------------------------------------------------------------------------
    | BANK ACCOUNT
    |--------------------------------------------------------------------------
    */

    public function bank()
    {
        $banks = BankAccount::where('user_id', Auth::user()->user_id)->get();
        return view('settings.bank', compact('banks'));
    }

    public function storeBank(Request $request)
    {
        // LIST BANK DIIZINKAN
        $allowedBanks = [
            'BCA', 'BRI', 'MANDIRI', 'BNI', 'JAGO', 'SEABANK', 'BSI', 'CIMB'
        ];

        // VALIDASI DIGIT TIAP BANK
        $bankDigitRules = [
            'BCA'     => 'digits_between:10,16',
            'BRI'     => 'digits_between:10,15',
            'MANDIRI' => 'digits_between:13,16',
            'BNI'     => 'digits:10',
            'JAGO'    => 'digits:15',
            'SEABANK' => 'digits:16',
            'BSI'     => 'digits_between:10,16',
            'CIMB'    => 'digits_between:10,13',
        ];

        $request->validate([
            'bank_name'    => ['required', Rule::in($allowedBanks)],
            'account_name' => ['required', 'string', 'max:120', 'regex:/^[a-zA-Z\s\.]+$/'],
            'account_no'   => ['required', 'numeric', $bankDigitRules[$request->bank_name]],
        ]);

        $isFirst = BankAccount::where('user_id', Auth::user()->user_id)->doesntExist();

        BankAccount::create([
            'user_id'      => Auth::user()->user_id,
            'bank_name'    => $request->bank_name,
            'account_name' => $request->account_name,
            'account_no'   => $request->account_no,
            'is_default'   => $isFirst ? 1 : 0,
        ]);

        return back()->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function destroyBank($id)
    {
        $bank = BankAccount::where('user_id', Auth::user()->user_id)
                        ->where('bank_account_id', $id)
                        ->firstOrFail();

        if ($bank->is_default) {
            return back()->with('error', 'Rekening utama tidak dapat dihapus.');
        }

        $bank->delete();
        return back()->with('success', 'Rekening berhasil dihapus.');
    }

    public function setDefaultBank($id)
    {
        DB::transaction(function () use ($id) {
            BankAccount::where('user_id', Auth::user()->user_id)->update(['is_default' => 0]);
            BankAccount::where('user_id', Auth::user()->user_id)
                    ->where('bank_account_id', $id)
                    ->update(['is_default' => 1]);
        });

        return back()->with('success', 'Rekening utama diperbarui.');
    }


    /*
    |--------------------------------------------------------------------------
    | ADDRESS
    |--------------------------------------------------------------------------
    */

    public function address()
    {
        $addresses = Address::where('user_id', Auth::user()->user_id)->get();
        return view('settings.address', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'label'       => 'required|string|max:60',
            'recipient'   => 'required|string|max:120|regex:/^[a-zA-Z\s\.]+$/',
            'phone'       => ['required', 'regex:/^8[0-9]{7,12}$/'],
            'line1'       => 'required|string|max:160',
            'city'        => 'required|string|max:100',
            'province'    => 'required|string|max:100',
            'postal_code' => 'required|numeric|digits:5',
        ]);

        $isFirst = Address::where('user_id', Auth::user()->user_id)->doesntExist();

        Address::create([
            'user_id'    => Auth::user()->user_id,
            'label'      => $request->label,
            'recipient'  => $request->recipient,
            'phone'      => $request->phone,
            'line1'      => $request->line1,
            'city'       => $request->city,
            'province'   => $request->province,
            'postal_code'=> $request->postal_code,
            'is_default' => $isFirst ? 1 : 0,
        ]);

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function destroyAddress($id)
    {
        $address = Address::where('user_id', Auth::user()->user_id)
                          ->where('address_id', $id)
                          ->firstOrFail();

        if ($address->is_default) {
            return back()->with('error', 'Alamat utama tidak dapat dihapus.');
        }

        $address->delete();
        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefaultAddress($id)
    {
        DB::transaction(function () use ($id) {
            Address::where('user_id', Auth::user()->user_id)->update(['is_default' => 0]);
            Address::where('user_id', Auth::user()->user_id)
                   ->where('address_id', $id)
                   ->update(['is_default' => 1]);
        });

        return back()->with('success', 'Alamat utama diperbarui.');
    }
}
