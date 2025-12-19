@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8">

    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pengaturan Profil</h1>
        <p class="text-gray-500 mt-2">Kelola informasi pribadi, alamat pengiriman, dan keamanan akun Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Sidebar Kiri: Kartu Profil -->
        <div class="lg:col-span-4 xl:col-span-3">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 text-center sticky top-24">
                <div class="relative w-32 h-32 mx-auto mb-5 group">
                    <img class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg group-hover:opacity-90 transition-opacity"
                         src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=EC1C25&color=fff&size=256' }}"
                         alt="Foto Profil">
                    <button class="absolute bottom-1 right-1 bg-gray-900 text-white p-2.5 rounded-full hover:bg-[#EC1C25] shadow-md transition-all duration-300 transform hover:scale-110" title="Ubah Foto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                </div>

                <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 font-medium mb-4">{{ $user->email }}</p>

                <div class="flex justify-center gap-2 mb-6">
                    <span class="bg-red-50 text-[#EC1C25] text-xs font-semibold px-3 py-1 rounded-full border border-red-100 uppercase tracking-wide">
                        {{ $user->role }}
                    </span>
                    <span class="bg-green-50 text-green-600 text-xs font-semibold px-3 py-1 rounded-full border border-green-100 uppercase tracking-wide">
                        {{ $user->status }}
                    </span>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <button onclick="openPasswordModal()" class="w-full text-gray-700 bg-white border border-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Ganti Kata Sandi
                    </button>
                </div>
            </div>
        </div>

        <!-- Konten Kanan: Tabs -->
        <div class="lg:col-span-8 xl:col-span-9">

            <!-- Tab Navigation -->
            <div class="mb-6 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="profileTab" data-tabs-toggle="#profileTabContent" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 data-[state=active]:text-[#EC1C25] data-[state=active]:border-[#EC1C25] transition-all" id="biodata-tab" data-tabs-target="#biodata" type="button" role="tab" aria-controls="biodata" aria-selected="false">
                            Data Diri
                        </button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all" id="address-tab" data-tabs-target="#address" type="button" role="tab" aria-controls="address" aria-selected="false">
                            Daftar Alamat
                        </button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all" id="bank-tab" data-tabs-target="#bank" type="button" role="tab" aria-controls="bank" aria-selected="false">
                            Rekening Bank
                        </button>
                    </li>
                </ul>
            </div>

            <div id="profileTabContent">

                <!-- TAB 1: DATA DIRI -->
                <div class="hidden p-1 rounded-lg" id="biodata" role="tabpanel" aria-labelledby="biodata-tab">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Ubah Biodata Diri</h3>

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ $user->name }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 transition-colors" required>
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Nomor Telepon / WhatsApp</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <input type="text" name="phone" value="{{ $user->profile->phone ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full ps-10 p-2.5 transition-colors" placeholder="08123456789">
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Bio Singkat</label>
                                    <textarea name="bio" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-[#EC1C25] focus:border-[#EC1C25] transition-colors" placeholder="Tulis sedikit tentang diri Anda...">{{ $user->profile->bio ?? '' }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Bio ini akan muncul di profil toko Anda jika Anda berjualan.</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-end border-t border-gray-100 pt-6">
                                <button type="submit" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-6 py-2.5 text-center shadow-md transition-all hover:shadow-lg">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TAB 2: ALAMAT -->
                <div class="hidden p-1 rounded-lg" id="address" role="tabpanel" aria-labelledby="address-tab">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Alamat Tersimpan</h3>
                                <p class="text-sm text-gray-500 mt-1">Alamat ini digunakan untuk pengiriman barang.</p>
                            </div>
                            <button onclick="openAddressModal()" class="text-white bg-gray-900 hover:bg-black focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center gap-2 shadow-sm hover:shadow transition-all" type="button">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah Alamat
                            </button>
                        </div>

                        <!-- List Alamat -->
                        <div class="grid gap-4">
                            @forelse($user->addresses as $addr)
                            <div class="group relative bg-white border border-gray-200 p-5 rounded-xl transition-all hover:shadow-md {{ $addr->is_default ? 'border-l-4 border-l-[#EC1C25] bg-red-50/10' : 'hover:border-gray-300' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-grow pr-8">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-bold text-gray-900 text-base">{{ $addr->label }}</span>
                                            @if($addr->is_default)
                                                <span class="bg-red-100 text-[#EC1C25] text-[10px] font-bold px-2.5 py-0.5 rounded border border-red-200 uppercase tracking-wide">Utama</span>
                                            @endif
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $addr->recipient }} <span class="text-gray-400 font-normal mx-1">•</span> {{ $addr->phone }}</p>
                                        <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                                            {{ $addr->detail_address }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $addr->village }}, {{ $addr->district }}, {{ $addr->city }}, {{ $addr->province }} <span class="font-medium text-gray-700">{{ $addr->postal_code }}</span>
                                        </p>
                                    </div>

                                    <!-- Action Menu -->
                                    <div class="flex flex-col gap-2 items-end sm:flex-row sm:items-center">
                                        <!-- Tombol Edit -->
                                        <button onclick="editAddress({{ json_encode($addr) }})" class="text-xs text-gray-600 hover:text-[#EC1C25] font-medium bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded flex items-center gap-1 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            Edit
                                        </button>

                                        @if(!$addr->is_default)
                                            <form action="{{ route('profile.address.default', $addr->address_id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline bg-blue-50 px-3 py-1.5 rounded transition-colors">Jadikan Utama</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('profile.address.delete', $addr->address_id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium hover:underline bg-red-50 px-3 py-1.5 rounded w-full text-left transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-gray-500 font-medium">Belum ada alamat tersimpan.</p>
                                <p class="text-xs text-gray-400 mt-1">Tambahkan alamat untuk memudahkan pengiriman barang Anda.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- TAB 3: BANK -->
                <div class="hidden p-1 rounded-lg" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Rekening Bank</h3>
                                <p class="text-sm text-gray-500 mt-1">Rekening ini digunakan untuk pencairan dana penjualan.</p>
                            </div>
                            <button onclick="openBankModal()" class="text-white bg-gray-900 hover:bg-black focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center gap-2 shadow-sm hover:shadow transition-all" type="button">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah Rekening
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($user->bankAccounts as $bank)
                            <div class="relative bg-gradient-to-br from-gray-800 to-gray-900 p-6 rounded-xl shadow-md text-white group overflow-hidden">
                                <!-- Tombol Aksi (Absolute) -->
                                <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <!-- Edit -->
                                    <button onclick="editBank({{ json_encode($bank) }})" class="bg-white/20 hover:bg-white/40 p-1.5 rounded-full backdrop-blur-sm transition-colors text-white" title="Edit Rekening">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <!-- Hapus -->
                                    <form action="{{ route('profile.bank.delete', $bank->bank_account_id) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?');">
                                        @csrf @method('DELETE')
                                        <button class="bg-white/20 hover:bg-red-500 p-1.5 rounded-full backdrop-blur-sm transition-colors text-white" title="Hapus Rekening">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>

                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                                        <span class="font-medium text-gray-200 text-sm tracking-wider uppercase">{{ $bank->bank_name }}</span>
                                    </div>
                                    @if($bank->is_default)
                                        <span class="bg-green-500/20 text-green-300 text-[10px] px-2 py-0.5 rounded border border-green-500/30">Utama</span>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <p class="text-xs text-gray-400 mb-1">Nomor Rekening</p>
                                    <p class="text-2xl font-mono tracking-widest text-white">{{ chunk_split($bank->account_no, 4, ' ') }}</p>
                                </div>

                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-xs text-gray-400 mb-0.5">Pemilik</p>
                                        <p class="text-sm font-medium text-white uppercase">{{ Str::limit($bank->account_name, 20) }}</p>
                                    </div>
                                    <div class="opacity-50">
                                        <!-- Chip Icon Dummy -->
                                        <div class="w-8 h-6 border border-gray-400 rounded bg-gray-500/30"></div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <p class="text-gray-500 font-medium">Belum ada rekening bank.</p>
                                <p class="text-xs text-gray-400 mt-1">Tambahkan rekening untuk menerima pembayaran hasil penjualan.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Modal Tambah/Edit Alamat -->
<div id="address-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-gray-900/50">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-white">
                <h3 class="text-xl font-bold text-gray-800" id="address-modal-title">
                    Tambah Alamat Baru
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" onclick="closeAddressModal()">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Body -->
            <form id="address-form" action="{{ route('profile.address.add') }}" method="POST">
                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    @csrf
                    <!-- Container Method PUT untuk Edit -->
                    <div id="address-method"></div>

                    <div class="grid gap-6 mb-6 grid-cols-2">
                        <!-- Label & Info Penerima -->
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Label Alamat <span class="text-red-500">*</span></label>
                            <input type="text" name="label" id="addr_label" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 transition-shadow" placeholder="Contoh: Rumah, Kost" required>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Nama Penerima <span class="text-red-500">*</span></label>
                            <input type="text" name="recipient" id="addr_recipient" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 transition-shadow" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">No HP Penerima <span class="text-red-500">*</span></label>
                            <input type="number" name="phone" id="addr_phone" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 transition-shadow" placeholder="08..." required>
                        </div>

                        <!-- Area API Wilayah (Background Halus) -->
                        <div class="col-span-2 bg-gray-50/80 p-5 rounded-xl border border-gray-200 grid grid-cols-2 gap-5">
                            <div class="col-span-2">
                                <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lokasi Wilayah
                                </h4>
                                <!-- Info Saat Edit -->
                                <p id="current-location-info" class="text-xs text-gray-500 hidden mb-2">Lokasi saat ini: <span class="font-medium text-gray-700" id="current-location-text"></span>. Pilih ulang di bawah jika ingin mengubah.</p>
                            </div>

                            <div class="col-span-2 sm:col-span-1">
                                <label class="block mb-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Provinsi</label>
                                <select id="select-province" name="province_name" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 cursor-pointer hover:border-gray-400 transition-colors">
                                    <option value="">Pilih Provinsi</option>
                                </select>
                                <input type="hidden" name="province_name" id="input-province">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block mb-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kota/Kabupaten</label>
                                <select id="select-city" name="city_name" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 cursor-pointer hover:border-gray-400 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed" disabled>
                                    <option value="">Pilih Kota</option>
                                </select>
                                <input type="hidden" name="city_name" id="input-city">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block mb-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kecamatan</label>
                                <select id="select-district" name="district_name" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 cursor-pointer hover:border-gray-400 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed" disabled>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                <input type="hidden" name="district_name" id="input-district">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block mb-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Desa/Kelurahan</label>
                                <select id="select-village" name="village_name" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 cursor-pointer hover:border-gray-400 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed" disabled>
                                    <option value="">Pilih Desa</option>
                                </select>
                                <input type="hidden" name="village_name" id="input-village">
                            </div>
                        </div>

                        <!-- Detail Lainnya -->
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Kode Pos <span class="text-red-500">*</span></label>
                            <input type="number" name="postal_code" id="addr_postal" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 font-mono" placeholder="40257" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Detail Jalan <span class="text-red-500">*</span></label>
                            <textarea name="detail_address" id="addr_detail" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#EC1C25] focus:border-[#EC1C25]" placeholder="Nama Jalan, Nomor Rumah, RT/RW, Patokan..." required></textarea>
                        </div>

                        <!-- Toggle Jadikan Utama -->
                        <div class="col-span-2 flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 mt-2">
                            <div>
                                <span class="block text-sm font-medium text-gray-900">Jadikan Alamat Utama</span>
                                <span class="block text-xs text-gray-500">Alamat ini akan digunakan sebagai prioritas pengiriman.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_default" id="addr_default" value="1" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#EC1C25]"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                    <button type="button" onclick="closeAddressModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 focus:outline-none bg-white rounded-lg border border-gray-300 hover:bg-gray-50 hover:text-gray-900 focus:z-10 focus:ring-4 focus:ring-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-6 py-2.5 text-center shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5">
                        Simpan Alamat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Modal Tambah/Edit Rekening (Desain Clean) -->
<div id="bank-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-gray-900/50">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-white">
                <h3 class="text-lg font-bold text-gray-800" id="bank-modal-title">
                    Tambah Rekening Bank
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" onclick="closeBankModal()">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Body -->
            <form id="bank-form" action="{{ route('profile.bank.add') }}" method="POST">
                <div class="p-6">
                    @csrf
                    <!-- Container Method PUT untuk Edit -->
                    <div id="bank-method"></div>

                    <div class="grid gap-5">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Nama Bank <span class="text-red-500">*</span></label>
                            <select name="bank_name" id="bank_name" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 transition-shadow" required>
                                <option value="">Pilih Bank</option>
                                <option value="BCA">BCA</option>
                                <option value="BNI">BNI</option>
                                <option value="BRI">BRI</option>
                                <option value="MANDIRI">MANDIRI</option>
                                <option value="CIMB">CIMB NIAGA</option>
                                <option value="JAGO">BANK JAGO</option>
                                <option value="SEABANK">SEABANK</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Nomor Rekening <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                </div>
                                <input type="number" name="account_no" id="bank_number" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full ps-10 p-2.5 font-mono" placeholder="Contoh: 1234567890" required>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                            <input type="text" name="account_name" id="bank_owner" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 uppercase" placeholder="Sesuai buku tabungan" required>
                            <p class="mt-2 text-xs text-gray-500 bg-yellow-50 p-2 rounded border border-yellow-100 flex gap-2">
                                <svg class="w-4 h-4 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Pastikan nama sesuai agar pencairan lancar.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                    <button type="button" onclick="closeBankModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 focus:outline-none bg-white rounded-lg border border-gray-300 hover:bg-gray-50 hover:text-gray-900 focus:z-10 focus:ring-4 focus:ring-gray-100 transition-all">Batal</button>
                    <button type="submit" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-6 py-2.5 text-center shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5">Simpan Rekening</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Modal Ganti Password (Desain Clean) -->
<div id="password-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-gray-900/50">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-white">
                <h3 class="text-lg font-bold text-gray-800">
                    Ganti Kata Sandi
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" data-modal-hide="password-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Body -->
            <form action="{{ route('profile.password.update') }}" method="POST">
                <div class="p-6">
                    @csrf @method('PUT')
                    <div class="grid gap-5">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 transition-shadow" placeholder="••••••••" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Kata Sandi Baru</label>
                            <input type="password" name="new_password" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 transition-shadow" placeholder="••••••••" required>
                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Min. 8 karakter, huruf besar, kecil, angka, & simbol.
                            </p>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="new_password_confirmation" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 transition-shadow" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                    <button data-modal-hide="password-modal" type="button" class="px-5 py-2.5 text-sm font-medium text-gray-700 focus:outline-none bg-white rounded-lg border border-gray-300 hover:bg-gray-50 hover:text-gray-900 focus:z-10 focus:ring-4 focus:ring-gray-100 transition-all">Batal</button>
                    <button type="submit" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-6 py-2.5 text-center shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script Management -->
<script>
    // --- Logic Modal Address (Create/Edit) ---
    const addrModal = document.getElementById('address-modal');
    const addrForm = document.getElementById('address-form');
    const addrTitle = document.getElementById('address-modal-title');
    const addrMethod = document.getElementById('address-method');
    const locInfo = document.getElementById('current-location-info');
    const locText = document.getElementById('current-location-text');

    // Instance Modal Flowbite
    let modalAddr = null;

    document.addEventListener('DOMContentLoaded', () => {
        if(window.Flowbite) {
            modalAddr = new Flowbite.default.Modal(addrModal);
        }
    });

    function openAddressModal() {
        // Reset to CREATE mode
        addrForm.reset();
        addrForm.action = "{{ route('profile.address.add') }}";
        addrTitle.innerText = "Tambah Alamat Baru";
        addrMethod.innerHTML = ""; // Hapus input hidden PUT
        locInfo.classList.add('hidden'); // Sembunyikan info lokasi lama

        // Reset Dropdowns
        document.getElementById('select-province').value = "";
        document.getElementById('select-city').innerHTML = '<option value="">Pilih Kota</option>';
        document.getElementById('select-city').disabled = true;
        // ... (reset other dropdowns if needed) ...

        const modal = new Flowbite.default.Modal(addrModal);
        modal.show();
    }

    function editAddress(data) {
        // Set to EDIT mode
        addrForm.action = "/profile/address/" + data.address_id; // Pastikan route update ada
        addrTitle.innerText = "Edit Alamat";
        addrMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Fill Data
        document.getElementById('addr_label').value = data.label;
        document.getElementById('addr_recipient').value = data.recipient;
        document.getElementById('addr_phone').value = data.phone;
        document.getElementById('addr_postal').value = data.postal_code;
        document.getElementById('addr_detail').value = data.detail_address;

        // Handle Default Toggle
        document.getElementById('addr_default').checked = data.is_default;

        // Handle Location Info
        // Karena dropdown diload via API, kita tampilkan teks lokasi lama saja
        locText.innerText = `${data.village}, ${data.district}, ${data.city}, ${data.province}`;
        locInfo.classList.remove('hidden');

        const modal = new Flowbite.default.Modal(addrModal);
        modal.show();
    }

    function closeAddressModal() {
        const modal = new Flowbite.default.Modal(addrModal);
        modal.hide();
    }

    // --- Logic Modal Bank (Create/Edit) ---
    const bankModal = document.getElementById('bank-modal');
    const bankForm = document.getElementById('bank-form');
    const bankTitle = document.getElementById('bank-modal-title');
    const bankMethod = document.getElementById('bank-method');

    function openBankModal() {
        bankForm.reset();
        bankForm.action = "{{ route('profile.bank.add') }}";
        bankTitle.innerText = "Tambah Rekening Bank";
        bankMethod.innerHTML = "";

        const modal = new Flowbite.default.Modal(bankModal);
        modal.show();
    }

    function editBank(data) {
        bankForm.action = "/profile/bank/" + data.bank_account_id; // Perlu route update di controller
        bankTitle.innerText = "Edit Rekening Bank";
        bankMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('bank_name').value = data.bank_name;
        document.getElementById('bank_number').value = data.account_no;
        document.getElementById('bank_owner').value = data.account_name;

        const modal = new Flowbite.default.Modal(bankModal);
        modal.show();
    }

    function closeBankModal() {
        const modal = new Flowbite.default.Modal(bankModal);
        modal.hide();
    }

    // --- Logic API Wilayah Indonesia (EMSIFA) ---
    document.addEventListener('DOMContentLoaded', function() {
        const provinceSelect = document.getElementById('select-province');
        const citySelect = document.getElementById('select-city');
        const districtSelect = document.getElementById('select-district');
        const villageSelect = document.getElementById('select-village');

        const inputProvince = document.getElementById('input-province');
        const inputCity = document.getElementById('input-city');
        const inputDistrict = document.getElementById('input-district');
        const inputVillage = document.getElementById('input-village');

        if(provinceSelect) {
            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(response => response.json())
                .then(provinces => {
                    provinces.forEach(province => {
                        let option = document.createElement('option');
                        option.value = province.id;
                        option.text = province.name;
                        provinceSelect.add(option);
                    });
                });

            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                inputProvince.value = this.options[this.selectedIndex].text;

                citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                citySelect.disabled = true;
                districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                districtSelect.disabled = true;
                villageSelect.innerHTML = '<option value="">Pilih Desa</option>';
                villageSelect.disabled = true;

                if (provinceId) {
                    citySelect.disabled = false;
                    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
                        .then(response => response.json())
                        .then(cities => {
                            cities.forEach(city => {
                                let option = document.createElement('option');
                                option.value = city.id;
                                option.text = city.name;
                                citySelect.add(option);
                            });
                        });
                }
            });

            citySelect.addEventListener('change', function() {
                const cityId = this.value;
                inputCity.value = this.options[this.selectedIndex].text;

                districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                districtSelect.disabled = true;
                villageSelect.innerHTML = '<option value="">Pilih Desa</option>';
                villageSelect.disabled = true;

                if (cityId) {
                    districtSelect.disabled = false;
                    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${cityId}.json`)
                        .then(response => response.json())
                        .then(districts => {
                            districts.forEach(district => {
                                let option = document.createElement('option');
                                option.value = district.id;
                                option.text = district.name;
                                districtSelect.add(option);
                            });
                        });
                }
            });

            districtSelect.addEventListener('change', function() {
                const districtId = this.value;
                inputDistrict.value = this.options[this.selectedIndex].text;

                villageSelect.innerHTML = '<option value="">Pilih Desa</option>';
                villageSelect.disabled = true;

                if (districtId) {
                    villageSelect.disabled = false;
                    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`)
                        .then(response => response.json())
                        .then(villages => {
                            villages.forEach(village => {
                                let option = document.createElement('option');
                                option.value = village.id;
                                option.text = village.name;
                                villageSelect.add(option);
                            });
                        });
                }
            });

            villageSelect.addEventListener('change', function() {
                inputVillage.value = this.options[this.selectedIndex].text;
            });
        }
    });

    // Helper function for Password Modal
    function openPasswordModal() {
        const modalEl = document.getElementById('password-modal');
        const modal = new Flowbite.default.Modal(modalEl);
        modal.show();
    }
</script>
@endsection
