@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit Pengguna</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi akun pengguna atau administrator.</p>
        </div>
        <a href="{{ route('admin.users') }}" class="text-gray-500 hover:text-gray-900 font-medium text-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-8">
        <form action="{{ route('admin.users.update_admin', $user->user_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- User Info Header -->
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <img class="h-16 w-16 rounded-full border-2 border-gray-100 object-cover" src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=3730a3&color=fff' }}" alt="">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span class="bg-gray-100 px-2 py-0.5 rounded text-xs font-mono">{{ $user->username }}</span>
                            <span>&bull;</span>
                            <span class="capitalize">{{ $user->role }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block mb-2 text-sm font-bold text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" required>
                    </div>

                    <div>
                        <label for="username" class="block mb-2 text-sm font-bold text-gray-700">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" required>
                    </div>

                    <div>
                        <label for="email" class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" required>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6 mt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Ubah Password (Opsional)
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block mb-2 text-sm font-bold text-gray-700">Password Baru</label>
                            <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" placeholder="Kosongkan jika tidak ubah">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block mb-2 text-sm font-bold text-gray-700">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 mt-2">
                    <a href="{{ route('admin.users') }}" class="text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-xl text-sm px-6 py-3 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="text-white bg-telu-red hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-xl text-sm px-6 py-3 shadow-lg transition-transform hover:-translate-y-0.5">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
