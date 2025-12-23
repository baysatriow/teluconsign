@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Page Header -->
    <div class="mb-12 animate-fade-in-down">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Pengaturan Akun</h1>
        <p class="mt-3 text-gray-500 text-lg">Kelola profil, keamanan, dan preferensi pengiriman Anda.</p>
    </div>

    <div class="lg:grid lg:grid-cols-12 lg:gap-x-12">
        <!-- Sidebar Navigation -->
        <aside class="py-6 lg:col-span-3">
            <nav class="space-y-1.5 sticky top-28 bg-white/50 backdrop-blur-sm p-4 rounded-2xl border border-gray-100 shadow-sm">
                <a href="#profile" id="nav-profile" class="group flex items-center px-4 py-3 text-sm font-bold bg-[#EC1C25] text-white rounded-xl shadow-lg shadow-red-100 transition-all active-nav" aria-current="page">
                    <svg class="flex-shrink-0 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Profil Saya
                </a>

                <a href="#address" id="nav-address" class="group flex items-center px-4 py-3 text-sm font-bold text-gray-600 rounded-xl hover:bg-red-50 hover:text-[#EC1C25] transition-all">
                    <svg class="flex-shrink-0 mr-3 h-5 w-5 text-gray-400 group-hover:text-[#EC1C25] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Daftar Alamat
                </a>

                <a href="#security" id="nav-security" class="group flex items-center px-4 py-3 text-sm font-bold text-gray-600 rounded-xl hover:bg-red-50 hover:text-[#EC1C25] transition-all">
                    <svg class="flex-shrink-0 mr-3 h-5 w-5 text-gray-400 group-hover:text-[#EC1C25] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Keamanan
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="space-y-8 lg:col-span-9 animate-fade-in-up" id="main-content">
            
            <!-- SECTION 1: PROFIL -->
            <section id="section-profile" class="bg-white shadow-xl shadow-gray-200/40 rounded-3xl overflow-hidden border border-gray-100 mb-8 transition-all hover:shadow-2xl hover:shadow-gray-200/50">
                <div class="px-8 py-10 sm:p-12">
                    <div class="mb-10 flex items-center gap-4">
                        <div class="w-1.5 h-8 bg-[#EC1C25] rounded-full"></div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-gray-900">Informasi Pribadi</h2>
                            <p class="mt-1 text-gray-500">Update foto dan detail identitas Anda.</p>
                        </div>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <!-- Photo Upload Section -->
                        <div class="bg-gray-50/50 rounded-2xl p-6 mb-10 border border-gray-100 flex flex-col sm:flex-row items-center gap-8">
                            <div class="relative group">
                                <div class="absolute inset-0 bg-red-100 rounded-full blur-2xl opacity-0 group-hover:opacity-40 transition-opacity"></div>
                                <img id="photo-preview" class="relative z-10 h-32 w-32 rounded-full object-cover ring-4 ring-white shadow-xl" 
                                     src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=EC1C25&color=fff' }}" 
                                     alt="Current profile photo">
                                <label for="photo-input" class="absolute bottom-1 right-1 z-20 bg-gray-900 text-white p-2.5 rounded-full shadow-lg cursor-pointer hover:bg-[#EC1C25] transition-all transform hover:scale-110 active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*" onchange="previewImage(this)">
                                </label>
                            </div>
                            <div class="text-center sm:text-left flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Foto Profil</h3>
                                <p class="text-sm text-gray-400 mb-4 max-w-xs">Pilih foto terbaik Anda. Ukuran maks 2MB (JPG atau PNG).</p>
                                <button type="button" onclick="document.getElementById('photo-input').click()" class="text-sm font-bold text-[#EC1C25] hover:underline transition-all">Ganti Foto</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-6 mb-10">
                            <div class="sm:col-span-3">
                                <label for="name" class="block text-sm font-bold text-gray-800 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ $user->name }}" class="input-modern block w-full bg-white border-gray-200 focus:ring-red-100 focus:border-[#EC1C25] transition-all" placeholder="Masukkan nama lengkap">
                            </div>

                            <div class="sm:col-span-3">
                                <label for="email" class="block text-sm font-bold text-gray-800 mb-2">Alamat Email</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none group-focus-within:text-[#EC1C25] transition-colors">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input type="email" value="{{ $user->email }}" disabled class="input-modern block w-full pl-11 bg-gray-50 border-gray-200 text-gray-400 cursor-not-allowed border-dashed" placeholder="email@domain.com">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="phone" class="block text-sm font-bold text-gray-800 mb-2">Nomor WhatsApp</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none group-focus-within:text-[#EC1C25] transition-colors">
                                        <span class="text-sm font-extrabold text-gray-400">+62</span>
                                    </div>
                                    <input type="text" name="phone" id="phone" value="{{ $user->profile->phone ?? '' }}" class="input-modern block w-full pl-12 bg-white border-gray-200 focus:ring-red-100 focus:border-[#EC1C25] transition-all" placeholder="812-3456-7890">
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <label for="bio" class="block text-sm font-bold text-gray-800 mb-2">Biografi Singkat</label>
                                <textarea id="bio" name="bio" rows="4" class="input-modern block w-full bg-white border-gray-200 focus:ring-red-100 focus:border-[#EC1C25] transition-all resize-none" placeholder="Tulis sedikit tentang diri Anda...">{{ $user->profile->bio ?? '' }}</textarea>
                                <p class="mt-2 text-xs text-gray-400 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Bio akan ditampilkan pada halaman profil publik Anda.</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-50">
                            <button type="submit" class="bg-[#EC1C25] text-white px-8 py-3.5 rounded-2xl font-extrabold text-sm shadow-xl shadow-red-100 hover:bg-[#c4161e] hover:shadow-2xl hover:shadow-red-200 transition-all transform hover:-translate-y-1 active:translate-y-0 active:shadow-lg">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- SECTION 2: ALAMAT -->
            <section id="section-address" class="hidden bg-white shadow-xl shadow-gray-200/40 rounded-3xl overflow-hidden border border-gray-100 mb-8 transition-all hover:shadow-2xl hover:shadow-gray-200/50">
                <div class="px-8 py-10 sm:p-12">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-12">
                        <div class="flex items-center gap-4">
                            <div class="w-1.5 h-8 bg-[#EC1C25] rounded-full"></div>
                            <div>
                                <h2 class="text-2xl font-extrabold text-gray-900">Daftar Alamat</h2>
                                <p class="mt-1 text-gray-500">Kelola tujuan pengiriman belanjaan Anda.</p>
                            </div>
                        </div>
                        <button onclick="openAddressModal()" type="button" class="group relative px-6 py-3 bg-gray-900 text-white font-extrabold rounded-2xl shadow-xl hover:bg-black transition-all transform hover:-translate-y-1 active:translate-y-0 overflow-hidden">
                            <span class="relative z-10 flex items-center gap-2">
                                <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Tambah Alamat
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-[#EC1C25] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </button>
                    </div>

                    <div class="grid gap-8 sm:grid-cols-2">
                        @forelse($user->addresses as $addr)
                        <div class="relative group rounded-3xl border-2 {{ $addr->is_default ? 'border-[#EC1C25] bg-red-50/10' : 'border-gray-50 bg-white hover:border-red-100' }} p-8 shadow-sm flex flex-col transition-all duration-300 hover:shadow-xl hover:shadow-gray-200/50">
                            @if($addr->is_default)
                                <span class="absolute -top-3 left-8 inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-[#EC1C25] text-white shadow-lg shadow-red-200 tracking-wider uppercase">
                                    Alamat Utama
                                </span>
                            @endif
                            
                            <div class="flex items-start justify-between mb-4 mt-2">
                                <h3 class="text-lg font-extrabold text-gray-900 flex items-center gap-2.5">
                                    <span class="p-2 rounded-xl {{ $addr->is_default ? 'bg-[#EC1C25] text-white' : 'bg-gray-100 text-gray-500' }} transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </span>
                                    {{ $addr->label }}
                                </h3>
                            </div>
                            
                            <div class="flex-1 mt-4 space-y-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-base font-bold text-gray-900">{{ $addr->recipient }}</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-sm font-medium text-gray-500">{{ $addr->phone }}</span>
                                </div>
                                <div class="text-sm text-gray-600 leading-relaxed bg-gray-50/50 p-4 rounded-2xl border border-gray-50 group-hover:bg-white group-hover:border-red-50 transition-colors">
                                    <p class="font-medium">{{ $addr->detail_address }}</p>
                                    <p class="text-xs mt-1 text-gray-400">{{ $addr->village }}, {{ $addr->district }}, {{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</p>
                                </div>
                            </div>

                            <div class="mt-8 flex items-center justify-between gap-4 pt-6 border-t border-gray-50">
                                <div class="flex items-center gap-5">
                                    <button type="button" onclick="editAddress({{ json_encode($addr) }})" class="text-sm font-extrabold text-gray-500 hover:text-[#EC1C25] transition-colors flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Ubah
                                    </button>
                                    @if(!$addr->is_default)
                                        <form action="{{ route('profile.address.default', $addr->address_id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-sm font-extrabold text-blue-600 hover:text-blue-800 transition-colors">Utamakan</button>
                                        </form>
                                    @endif
                                </div>
                                
                                @if(!$addr->is_default)
                                <form action="{{ route('profile.address.delete', $addr->address_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus alamat ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="sm:col-span-2 text-center py-20 rounded-3xl border-4 border-dashed border-gray-100 bg-gray-50/30">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-extrabold text-gray-900 mb-2">Belum ada alamat tersimpan</h3>
                            <p class="text-gray-400 max-w-sm mx-auto mb-8">Tambahkan alamat pengiriman untuk mempermudah proses checkout belanja Anda.</p>
                            <button type="button" onclick="openAddressModal()" class="inline-flex items-center px-8 py-3.5 bg-[#EC1C25] text-white font-extrabold rounded-2xl shadow-xl shadow-red-100 hover:bg-[#c4161e] transform transition-all hover:-translate-y-1 active:translate-y-0">
                                <svg class="mr-2.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Buat Alamat Baru
                            </button>
                        </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- SECTION 3: KEAMANAN -->
            <section id="section-security" class="hidden bg-white shadow-xl shadow-gray-200/40 rounded-3xl overflow-hidden border border-gray-100 mb-8 transition-all hover:shadow-2xl hover:shadow-gray-200/50">
                <div class="px-8 py-10 sm:p-12">
                     <div class="mb-12 flex items-center gap-4">
                        <div class="w-1.5 h-8 bg-[#EC1C25] rounded-full"></div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-gray-900">Keamanan Akun</h2>
                            <p class="mt-1 text-gray-500">Update password Anda secara berkala demi keamanan.</p>
                        </div>
                    </div>

                    <form action="{{ route('profile.password.update') }}" method="POST" class="max-w-2xl">
                        @csrf @method('PUT')
                        <div class="space-y-8">
                            <div>
                                <label for="current_password" class="block text-sm font-bold text-gray-800 mb-2">Password Saat Ini</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none group-focus-within:text-[#EC1C25] transition-colors">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </div>
                                    <input type="password" name="current_password" id="current_password" class="input-modern block w-full pl-11 bg-white border-gray-200 focus:ring-red-100 focus:border-[#EC1C25] transition-all" required placeholder="••••••••">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label for="new_password" class="block text-sm font-bold text-gray-800 mb-2">Password Baru</label>
                                    <input type="password" name="new_password" id="new_password" class="input-modern block w-full bg-white border-gray-200 focus:ring-red-100 focus:border-[#EC1C25] transition-all" required placeholder="Min 8 karakter">
                                </div>
                                <div>
                                    <label for="new_password_confirmation" class="block text-sm font-bold text-gray-800 mb-2">Konfirmasi Password</label>
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="input-modern block w-full bg-white border-gray-200 focus:ring-red-100 focus:border-[#EC1C25] transition-all" required placeholder="Ulangi password baru">
                                </div>
                            </div>

                            <div class="flex justify-end pt-8 border-t border-gray-50">
                                <button type="submit" class="bg-gray-900 text-white px-8 py-3.5 rounded-2xl font-extrabold text-sm shadow-xl hover:bg-black transition-all transform hover:-translate-y-1 active:translate-y-0">
                                    Update Password Sekarang
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
