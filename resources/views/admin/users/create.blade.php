@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Tambah Administrator</h1>
            <p class="text-sm text-gray-500 mt-1">Tambahkan akun administrator baru untuk mengelola platform.</p>
        </div>
        <a href="{{ route('admin.users') }}" class="text-gray-500 hover:text-gray-900 font-medium text-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-8">
        <form action="{{ route('admin.users.store_admin') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block mb-2 text-sm font-bold text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" placeholder="Contoh: Budi Santoso" required>
                    </div>

                    <div>
                        <label for="username" class="block mb-2 text-sm font-bold text-gray-700">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full ps-10 p-3 transition-all" placeholder="admin_user" required>
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full ps-10 p-3 transition-all" placeholder="admin@example.com" required>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="phone" class="block mb-2 text-sm font-bold text-gray-700">Nomor WhatsApp</label>
                        <input type="number" name="phone" id="phone" value="{{ old('phone') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" placeholder="6281234567890" required>
                    </div>

                    <div>
                        <label for="password" class="block mb-2 text-sm font-bold text-gray-700">Password</label>
                        <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" placeholder="••••••••" required>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block mb-2 text-sm font-bold text-gray-700">Ulangi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 mt-6">
                    <a href="{{ route('admin.users') }}" class="text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-xl text-sm px-6 py-3 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="text-white bg-telu-red hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-xl text-sm px-6 py-3 shadow-lg hover:shadow-red-500/30 transition-transform hover:-translate-y-0.5">
                        Simpan Admin
                    </button>
                </div>
            </div>
        </form>
    </div>
    </div>
</div>

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menambahkan Admin',
                html: `
                    <div class="text-left text-sm text-gray-600">
                        <p class="mb-2 font-bold">Terjadi kesalahan validasi:</p>
                        <ul class="list-disc pl-5 space-y-1 text-red-600">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                `,
                confirmButtonColor: '#EC1C25',
                confirmButtonText: 'Perbaiki Data',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6 py-2 font-bold'
                }
            });
        });
    </script>
@endif

@endsection
