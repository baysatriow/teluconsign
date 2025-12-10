@extends('layouts.settings')

@section('settings_content')

<div class="mb-8">
    <h1 class="text-2xl font-bold text-[var(--tc-text-main)]">Informasi Profil</h1>
    <p class="text-gray-500">Kelola informasi profil Anda untuk mengamankan akun.</p>
</div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false});
        });
    </script>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-8">
        @csrf
        @method('PUT')

        <div class="flex flex-col md:flex-row gap-10 items-start">

            <div class="w-full md:w-1/3 flex flex-col items-center pt-2">
                <div class="relative group">
                    <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-white shadow-lg ring-2 ring-gray-200 bg-gray-100 relative">

                        <img id="profile-preview"
                             src="{{ $user->photo_url ? asset('storage/' . $user->photo_url) : asset('images/default-avatar.png') }}"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=256';"
                             alt="Profile"
                             class="w-full h-full object-cover">

                        <div id="loading-overlay" class="absolute inset-0 bg-black bg-opacity-25 hidden items-center justify-center">
                            <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>

                    <label for="photo_input" class="absolute bottom-2 right-2 bg-[var(--tc-btn-bg)] hover:bg-[var(--tc-btn-bg-hover)] text-white p-3 rounded-full shadow-lg cursor-pointer transition-transform hover:scale-110 border-2 border-white z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </label>

                    <input type="file" name="photo" id="photo_input" class="hidden" accept="image/*" onchange="previewImage(event)">
                </div>
                <p class="text-xs text-gray-500 mt-4 text-center leading-relaxed">
                    Format: .JPG, .PNG<br>
                    Maksimal ukuran: 2MB
                </p>
            </div>

            <div class="w-full md:w-2/3 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="w-full rounded-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-2.5 text-sm font-medium"
                               oninput="this.value = this.value.replace(/[^a-zA-Z\s\.]/g, '')">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full rounded-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-2.5 text-sm font-medium">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">No. Handphone</label>
                        <div class="flex rounded-lg shadow-sm">
                            <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 text-gray-600 font-bold text-sm">+62</span>
                            <input type="text" name="phone" value="{{ old('phone', $user->profile?->phone) }}"
                                   class="flex-1 rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-2.5 text-sm font-medium"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Bio Singkat</label>
                        <textarea name="bio" rows="3"
                                  class="w-full rounded-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-3 text-sm font-medium">{{ old('bio', $user->profile?->bio) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-[var(--tc-btn-bg)] hover:bg-[var(--tc-btn-bg-hover)] text-white px-8 py-3 rounded-lg text-sm font-bold shadow-lg transition-transform transform active:scale-95">
                        Simpan Profil
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-900">Ganti Password</h3>
    </div>
    <form action="{{ route('password.update') }}" method="POST" class="p-8 bg-gray-50/30">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password Lama</label>
                <input type="password" name="current_password" class="w-full rounded-lg bg-white border border-gray-300 p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password Baru</label>
                <input type="password" name="password" class="w-full rounded-lg bg-white border border-gray-300 p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-lg bg-white border border-gray-300 p-2.5 text-sm">
            </div>
        </div>
        <div class="flex justify-end pt-6">
            <button type="submit" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-6 py-2.5 rounded-lg text-sm font-bold shadow-sm">Update Password</button>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        const imageField = document.getElementById('profile-preview');

        reader.onload = function(){
            if(reader.readyState == 2){
                imageField.src = reader.result;
            }
        }

        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>

@endsection
