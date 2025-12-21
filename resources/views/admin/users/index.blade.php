@extends('layouts.admin')

@section('content')
<!-- CSS DataTables Custom (Hanya untuk Admin Table jika masih diperlukan, tapi kita buat simple saja) -->
<style>
    /* Hide modal when Alpine loads */
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ addAdminModalOpen: false }">

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola akses, peran, dan status akun pengguna di platform.</p>
        </div>
        <button @click="addAdminModalOpen = true" class="text-white bg-gray-900 hover:bg-black focus:ring-4 focus:ring-gray-300 font-bold rounded-xl text-sm px-5 py-3 flex items-center gap-2 shadow-lg transition-all transform hover:-translate-y-0.5" type="button">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Admin
        </button>
    </div>

    <!-- Statistik Mini Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Member</div>
            <div class="flex items-end justify-between">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Administrator</div>
            <div class="flex items-end justify-between">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_admins']) }}</div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
            </div>
        </div>
        <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Akun Aktif</div>
            <div class="flex items-end justify-between">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_users']) }}</div>
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Disuspend</div>
            <div class="flex items-end justify-between">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['suspended_users']) }}</div>
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col space-y-8">

        <!-- TABEL 1: ADMINISTRATOR -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Administrator</h3>
                </div>
            </div>
            
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4">Admin</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Username</th>
                            <th class="px-6 py-4">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($admins as $admin)
                        <tr class="bg-white hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                <div class="flex items-center gap-3">
                                    <img class="h-10 w-10 rounded-full border border-gray-200 object-cover" src="{{ $admin->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($admin->name).'&background=3730a3&color=fff' }}" alt="">
                                    <span>{{ $admin->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $admin->email }}</td>
                            <td class="px-6 py-4"><span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg font-mono text-xs">{{ $admin->username }}</span></td>
                            <td class="px-6 py-4 text-gray-500">{{ $admin->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL 2: DAFTAR PENGGUNA -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-6">
            
            <!-- Search & Filter Form (Mirip Product Index) -->
            <form action="{{ route('admin.users') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4 mb-6">
                <div class="relative w-full md:flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full pl-10 p-3 transition-all" placeholder="Cari nama, email, atau username...">
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <select name="status" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full md:w-36 p-3 transition-all">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>

                    <select name="role" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full md:w-36 p-3 transition-all">
                        <option value="">Semua Role</option>
                        <option value="buyer" {{ request('role') == 'buyer' ? 'selected' : '' }}>Buyer</option>
                        <option value="seller" {{ request('role') == 'seller' ? 'selected' : '' }}>Seller</option>
                    </select>

                    <button type="submit" class="text-white bg-gray-900 hover:bg-black font-bold rounded-xl text-sm px-6 py-3 shadow-lg transition-transform hover:-translate-y-0.5">
                        Filter
                    </button>
                    
                    @if(request()->has('q') || request()->has('status') || request()->has('role'))
                        <a href="{{ route('admin.users') }}" class="text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 font-bold rounded-xl text-sm px-4 py-3 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4">Pengguna</th>
                            <th class="px-6 py-4">Kontak</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Bergabung</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr class="bg-white hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-900">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <img class="h-10 w-10 rounded-full object-cover border border-gray-200" src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" alt="">
                                        <span class="absolute bottom-0 right-0 w-3 h-3 border-2 border-white rounded-full {{ $user->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-400 font-mono">@ {{ $user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-700 font-medium">{{ $user->email }}</span>
                                    <span class="text-xs text-gray-400">{{ $user->profile->phone ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $user->role === 'seller' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->status === 'active')
                                    <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                    </span>
                                @elseif($user->status === 'suspended')
                                    <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Suspended
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1 rounded-full">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs text-nowrap">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.users.show', $user->user_id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border border-transparent hover:border-indigo-100" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    <form action="{{ route('admin.users.toggle_status', $user->user_id) }}" method="POST" id="status-form-{{ $user->user_id }}">
                                        @csrf
                                        @method('PATCH')
                                        @if($user->status === 'suspended')
                                            <button type="button" onclick="confirmAction('{{ $user->user_id }}', 'aktifkan')" class="text-green-600 hover:bg-green-50 p-2 rounded-lg transition-colors" title="Aktifkan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </button>
                                        @else
                                            <button type="button" onclick="confirmAction('{{ $user->user_id }}', 'suspend')" class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition-colors" title="Suspend">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500 py-12">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    </div>
                                    <p class="font-medium">Tidak ada pengguna ditemukan.</p>
                                    <p class="text-xs mt-1">Coba ubah filter pencarian anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination (Style Tailwind) -->
            <div class="mt-6">
                {{ $users->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah Admin (Alpine Controlled) -->
    <div x-show="addAdminModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div x-show="addAdminModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="addAdminModalOpen = false"></div>
        
        <!-- Modal Content -->
        <div x-show="addAdminModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
            
            <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900">
                    Tambah Administrator
                </h3>
                <button type="button" @click="addAdminModalOpen = false" class="text-gray-400 hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.users.store_admin') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4 mb-6">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-bold text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" placeholder="John Doe" required>
                    </div>
                    <div>
                        <label for="username" class="block mb-2 text-sm font-bold text-gray-700">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full ps-10 p-3 transition-all" placeholder="admin_user" required>
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full ps-10 p-3 transition-all" placeholder="admin@example.com" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block mb-2 text-sm font-bold text-gray-700">Password</label>
                            <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" placeholder="••••••••" required>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block mb-2 text-sm font-bold text-gray-700">Konfirmasi</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full p-3 transition-all" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end border-t border-gray-100 pt-6">
                    <button type="button" @click="addAdminModalOpen = false" class="mr-3 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-xl text-sm px-6 py-3">
                        Batal
                    </button>
                    <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-bold rounded-xl text-sm px-6 py-3 text-center shadow-lg transition-transform transform hover:-translate-y-0.5 w-full md:w-auto">
                        Simpan Admin
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    // Konfirmasi Aksi SweetAlert
    function confirmAction(userId, action) {
        let titleText = action === 'suspend' ? 'Suspend Akun Pengguna?' : 'Aktifkan Kembali Akun?';
        let confirmText = action === 'suspend' ? 'Ya, Suspend!' : 'Ya, Aktifkan!';
        let btnColor = action === 'suspend' ? '#EF4444' : '#10B981'; 

        Swal.fire({
            title: titleText,
            text: "Pastikan tindakan ini sudah sesuai prosedur.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#E5E7EB', 
            confirmButtonText: confirmText,
            cancelButtonText: '<span class="text-gray-800 font-medium">Batal</span>', 
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-4 py-2 font-bold shadow-lg',
                cancelButton: 'rounded-xl px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('status-form-' + userId).submit();
            }
        });
    }
</script>
@endsection
