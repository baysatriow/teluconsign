@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Page Header -->
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pengaturan Akun</h1>
        <p class="mt-2 text-gray-500">Kelola profil, keamanan, dan preferensi pengiriman Anda.</p>
    </div>

    <div class="lg:grid lg:grid-cols-12 lg:gap-x-12">
        <!-- Sidebar Navigation -->
        <aside class="py-6 lg:col-span-3">
            <nav class="space-y-2 sticky top-24">
                <a href="#profile" id="nav-profile" class="group flex items-center px-3 py-2 text-sm font-medium bg-red-50 text-[#EC1C25] rounded-md hover:bg-red-50 hover:text-[#EC1C25] transition-all" aria-current="page">
                    <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 text-[#EC1C25] group-hover:text-[#EC1C25]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="truncate">Profil Saya</span>
                </a>

                <a href="#address" id="nav-address" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-all">
                    <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="truncate">Daftar Alamat</span>
                </a>

                <a href="#security" id="nav-security" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-all">
                    <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span class="truncate">Keamanan & Password</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="space-y-6 lg:col-span-9" id="main-content">
            
            <!-- SECTION 1: PROFIL -->
            <section id="section-profile" class="bg-white shadow rounded-2xl overflow-hidden mb-10 border border-gray-100">
                <div class="px-6 py-8 sm:p-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Ubah Profil</h2>
                            <p class="mt-1 text-sm text-gray-500">Update foto dan informasi data diri Anda.</p>
                        </div>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <!-- Photo Upload -->
                        <div class="flex flex-col sm:flex-row items-center gap-8 mb-10">
                            <div class="relative group">
                                <img id="photo-preview" class="h-32 w-32 rounded-full object-cover ring-4 ring-white shadow-lg" 
                                     src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=EC1C25&color=fff' }}" 
                                     alt="Current profile photo">
                                <label for="photo-input" class="absolute bottom-1 right-1 bg-gray-900 text-white p-2.5 rounded-full shadow-lg cursor-pointer hover:bg-[#EC1C25] transition-all transform hover:scale-110">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*" onchange="previewImage(this)">
                                </label>
                            </div>
                            <div class="text-center sm:text-left">
                                <h3 class="text-lg font-medium text-gray-900">Foto Profil</h3>
                                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <div class="mt-1">
                                    <input type="text" name="name" id="name" value="{{ $user->name }}" class="shadow-sm focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full sm:text-sm border-gray-300 rounded-md transition-shadow">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <div class="mt-1">
                                    <input type="email" value="{{ $user->email }}" disabled class="bg-gray-50 shadow-sm block w-full sm:text-sm border-gray-300 rounded-md text-gray-500 cursor-not-allowed">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon / WA</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">+62</span>
                                    </div>
                                    <input type="text" name="phone" id="phone" value="{{ $user->profile->phone ?? '' }}" class="focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full pl-12 sm:text-sm border-gray-300 rounded-md" placeholder="81234567890">
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <label for="bio" class="block text-sm font-medium text-gray-700">Bio Singkat</label>
                                <div class="mt-1">
                                    <textarea id="bio" name="bio" rows="3" class="shadow-sm focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full sm:text-sm border border-gray-300 rounded-md">{{ $user->profile->bio ?? '' }}</textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Tulis sedikit tentang diri Anda untuk ditampilkan di profil.</p>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="bg-[#EC1C25] border border-transparent rounded-md shadow-sm py-2 px-6 inline-flex justify-center text-sm font-medium text-white hover:bg-[#c4161e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all hover:shadow-lg transform hover:-translate-y-0.5">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- SECTION 2: ALAMAT -->
            <section id="section-address" class="hidden bg-white shadow rounded-2xl overflow-hidden mb-10 border border-gray-100">
                <div class="px-6 py-8 sm:p-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Daftar Alamat</h2>
                            <p class="mt-1 text-sm text-gray-500">Kelola alamat pengiriman untuk checkout belanja.</p>
                        </div>
                        <button onclick="openAddressModal()" type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Tambah Alamat
                        </button>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        @forelse($user->addresses as $addr)
                        <div class="relative rounded-xl border {{ $addr->is_default ? 'border-red-200 bg-red-50/20 ring-1 ring-red-200' : 'border-gray-200 bg-white hover:border-gray-300' }} p-6 shadow-sm flex flex-col transition-all">
                            @if($addr->is_default)
                                <span class="absolute top-4 right-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Utama
                                </span>
                            @endif
                            
                            <h3 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                {{ $addr->label }}
                            </h3>
                            
                            <div class="flex-1 mt-2 text-sm text-gray-500 space-y-1">
                                <p class="font-medium text-gray-900">{{ $addr->recipient }} <span class="text-gray-300 mx-1">|</span> {{ $addr->phone }}</p>
                                <p>{{ $addr->detail_address }}</p>
                                <p>{{ $addr->village }}, {{ $addr->district }}</p>
                                <p>{{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</p>
                            </div>

                            <div class="mt-6 flex items-center gap-4 pt-4 border-t border-gray-100">
                                <button type="button" onclick="editAddress({{ json_encode($addr) }})" class="text-sm font-medium text-gray-600 hover:text-[#EC1C25] transition-colors">Edit</button>
                                
                                @if(!$addr->is_default)
                                    <form action="{{ route('profile.address.default', $addr->address_id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">Set Utama</button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('profile.address.delete', $addr->address_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus alamat ini?');" class="ml-auto">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800 transition-colors">Hapus</button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="sm:col-span-2 text-center py-12 rounded-xl border-2 border-dashed border-gray-300">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada alamat</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan alamat baru Anda.</p>
                            <div class="mt-6">
                                <button type="button" onclick="openAddressModal()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Tambah Alamat Baru
                                </button>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- SECTION 3: KEAMANAN -->
            <section id="section-security" class="hidden bg-white shadow rounded-2xl overflow-hidden mb-10 border border-gray-100">
                <div class="px-6 py-8 sm:p-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Keamanan Akun</h2>
                            <p class="mt-1 text-sm text-gray-500">Update password untuk menjaga keamanan akun Anda.</p>
                        </div>
                    </div>

                    <form action="{{ route('profile.password.update') }}" method="POST" class="max-w-xl">
                        @csrf @method('PUT')
                        <div class="space-y-6">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                                <div class="mt-1">
                                    <input type="password" name="current_password" id="current_password" class="shadow-sm focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                            </div>

                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                                <div class="mt-1">
                                    <input type="password" name="new_password" id="new_password" class="shadow-sm focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                            </div>

                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                <div class="mt-1">
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="shadow-sm focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" class="bg-gray-900 border border-transparent rounded-md shadow-sm py-2 px-6 inline-flex justify-center text-sm font-medium text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all">
                                    Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

        </div>
    </div>
</div>

<!-- ADDRESS MODAL -->
<div id="address-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-gray-900/50">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-white">
                <h3 class="text-xl font-bold text-gray-800" id="address-modal-title">Tambah Alamat Baru</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" onclick="closeAddressModal()">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>

            <form id="address-form" action="{{ route('profile.address.add') }}" method="POST">
                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    @csrf
                    <div id="address-method"></div>

                    <div class="grid gap-6 mb-6 grid-cols-2">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Label <span class="text-red-500">*</span></label>
                            <input type="text" name="label" id="addr_label" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" placeholder="Rumah, Kost, Kantor" required>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Penerima <span class="text-red-500">*</span></label>
                            <input type="text" name="recipient" id="addr_recipient" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">No HP <span class="text-red-500">*</span></label>
                            <input type="number" name="phone" id="addr_phone" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                        </div>

                        <!-- SEARCH LOCATION -->
                        <div class="col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <label class="block mb-2 text-sm font-semibold text-gray-800">Cari Lokasi (Kecamatan / Kota) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" id="location-search" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 pr-10" placeholder="Ketik min. 3 huruf..." autocomplete="off">
                                <div id="loading-spinner" class="absolute right-3 top-2.5 hidden">
                                    <svg class="animate-spin h-5 w-5 text-[#EC1C25]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                                <ul id="location-results" class="absolute z-20 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 max-h-60 overflow-y-auto hidden"></ul>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pilih dari daftar untuk auto-fill data wilayah.</p>

                            <!-- FIXED INPUT NAMES: province, city, district, village -->
                            <input type="hidden" name="location_id" id="input-location-id">
                            <input type="hidden" name="province" id="input-province">
                            <input type="hidden" name="city" id="input-city">
                            <input type="hidden" name="district" id="input-district">
                            <input type="hidden" name="village" id="input-village">
                        </div>

                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Kode Pos <span class="text-red-500">*</span></label>
                            <input type="number" name="postal_code" id="addr_postal" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Detail Alamat <span class="text-red-500">*</span></label>
                            <textarea name="detail_address" id="addr_detail" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#EC1C25] focus:border-[#EC1C25]" placeholder="Nama Jalan, No Rumah, RT/RW..." required></textarea>
                        </div>

                        <div class="col-span-2 flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-900">Jadikan Utama</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_default" id="addr_default" value="1" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#EC1C25]"></div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100 bg-gray-50/50">
                    <button type="button" onclick="closeAddressModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] font-medium rounded-lg text-sm px-6 py-2.5 shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Navigation Logic (Simple Tab Switching)
    const navLinks = document.querySelectorAll('aside nav a');
    const sections = document.querySelectorAll('#main-content > section');

    function setActiveTab(hash) {
        // Default to #profile if empty
        if(!hash) hash = '#profile';
        
        // Update Nav Styles
        navLinks.forEach(link => {
            const isTarget = link.getAttribute('href') === hash;
            link.className = isTarget 
                ? 'group flex items-center px-3 py-2 text-sm font-medium bg-red-50 text-[#EC1C25] rounded-md transition-all'
                : 'group flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-all';
            
            const svg = link.querySelector('svg');
            if(svg) {
                svg.className = isTarget
                    ? 'flex-shrink-0 -ml-1 mr-3 h-6 w-6 text-[#EC1C25]'
                    : 'flex-shrink-0 -ml-1 mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500';
            }
        });

        // Show/Hide Sections
        sections.forEach(sec => {
            if('#section-' + sec.id.split('-')[1] === '#section-' + hash.substring(1)) {
                sec.classList.remove('hidden');
            } else {
                sec.classList.add('hidden');
            }
        });
    }

    window.addEventListener('hashchange', () => setActiveTab(window.location.hash));
    document.addEventListener('DOMContentLoaded', () => setActiveTab(window.location.hash));

    // Photo Preview
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Address Modal Logic
    const addrModal = document.getElementById('address-modal');
    const addrForm = document.getElementById('address-form');
    const addrTitle = document.getElementById('address-modal-title');
    const addrMethod = document.getElementById('address-method');
    let modalInstance = null;

    document.addEventListener('DOMContentLoaded', () => {
        if(window.Flowbite) modalInstance = new Flowbite.default.Modal(addrModal);
    });

    function openAddressModal() {
        addrForm.reset();
        addrForm.action = "{{ route('profile.address.add') }}";
        addrTitle.innerText = "Tambah Alamat Baru";
        addrMethod.innerHTML = "";
        
        // Clear Hiddens
        document.getElementById('input-location-id').value = "";
        document.getElementById('input-province').value = "";
        document.getElementById('input-city').value = "";
        document.getElementById('input-district').value = "";
        document.getElementById('input-village').value = "";
        document.getElementById('location-search').value = "";

        if(modalInstance) modalInstance.show();
        else addrModal.classList.remove('hidden');
    }

    function editAddress(data) {
        addrForm.action = "/profile/address/" + data.address_id;
        addrTitle.innerText = "Edit Alamat";
        addrMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('addr_label').value = data.label;
        document.getElementById('addr_recipient').value = data.recipient;
        document.getElementById('addr_phone').value = data.phone;
        document.getElementById('addr_postal').value = data.postal_code;
        document.getElementById('addr_detail').value = data.detail_address;

        // Fill Hiddens
        document.getElementById('input-location-id').value = data.location_id ?? '';
        document.getElementById('input-province').value = data.province;
        document.getElementById('input-city').value = data.city;
        document.getElementById('input-district').value = data.district;
        document.getElementById('input-village').value = data.village;
        
        document.getElementById('location-search').value = `${data.village}, ${data.district}, ${data.city}, ${data.province}`;
        document.getElementById('addr_default').checked = data.is_default;

        if(modalInstance) modalInstance.show();
        else addrModal.classList.remove('hidden');
    }

    function closeAddressModal() {
        if(modalInstance) modalInstance.hide();
        else addrModal.classList.add('hidden');
    }

    // Autocomplete Logic
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('location-search');
        const resultsList = document.getElementById('location-results');
        const spinner = document.getElementById('loading-spinner');
        
        // Target Inputs (Fixed Names: province, city, etc)
        const inputLocId = document.getElementById('input-location-id');
        const inputProvince = document.getElementById('input-province');
        const inputCity = document.getElementById('input-city');
        const inputDistrict = document.getElementById('input-district');
        const inputVillage = document.getElementById('input-village');
        const inputZip = document.getElementById('addr_postal');

        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value;

            if (query.length < 3) {
                resultsList.classList.add('hidden');
                return;
            }

            spinner.classList.remove('hidden');

            debounceTimer = setTimeout(() => {
                fetch(`/location/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        resultsList.innerHTML = '';
                        spinner.classList.add('hidden');

                        if (data.length === 0) {
                            resultsList.innerHTML = '<li class="px-4 py-2 text-sm text-gray-500">Lokasi tidak ditemukan</li>';
                            resultsList.classList.remove('hidden');
                            return;
                        }

                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 text-sm text-gray-700 hover:bg-red-50 cursor-pointer border-b border-gray-100 last:border-0';
                            li.textContent = item.label; // e.g., "BATUNUNGGAL, BANDUNG..."
                            
                            li.addEventListener('click', () => {
                                searchInput.value = item.label;
                                // MAP API RESPONSE TO CORRECT FIELDS
                                inputLocId.value = item.id;
                                inputProvince.value = item.province_name; // API returns _name
                                inputCity.value = item.city_name;
                                inputDistrict.value = item.district_name;
                                inputVillage.value = item.subdistrict_name; // Map subdistrict -> village
                                inputZip.value = item.zip_code;

                                resultsList.classList.add('hidden');
                            });

                            resultsList.appendChild(li);
                        });
                        resultsList.classList.remove('hidden');
                    })
                    .catch(e => {
                        console.error(e);
                        spinner.classList.add('hidden');
                    });
            }, 500);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
                resultsList.classList.add('hidden');
            }
        });
    });
</script>
@endsection
