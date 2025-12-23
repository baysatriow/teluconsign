@extends('layouts.admin')

@section('content')
<!-- CSS DataTables Custom (Hanya untuk Admin Table jika masih diperlukan, tapi kita buat simple saja) -->
<style>
    /* Hide modal when Alpine loads */
    [x-cloak] { display: none !important; }
</style>

<div class="">

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola akses, peran, dan status akun pengguna di platform.</p>
        </div>
        @if(auth()->id() == 1)
        <a href="{{ route('admin.users.create') }}" class="text-white bg-telu-red hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-bold rounded-xl text-sm px-5 py-3 flex items-center gap-2 shadow-lg hover:shadow-red-500/30 transition-all transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Admin
        </a>
        @endif
    </div>



    <!-- Statistik Mini Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform hover-red-b">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Member</div>
            <div class="flex items-end justify-between">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform hover-red-b">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Administrator</div>
            <div class="flex items-end justify-between">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_admins']) }}</div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
            </div>
        </div>
        <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform hover-red-b">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Akun Aktif</div>
            <div class="flex items-end justify-between">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_users']) }}</div>
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform hover-red-b">
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
                            <td class="px-6 py-4 text-gray-500">
                                <div class="flex items-center gap-2">
                                     <span>{{ $admin->created_at->format('d M Y') }}</span>
                                     
                                     <!-- Edit: Only for Super Admin OR Self -->
                                     @if(auth()->id() == 1 || auth()->id() == $admin->user_id)
                                        <a href="{{ route('admin.users.edit', $admin->user_id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-lg transition-colors" title="Edit Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                     @endif

                                     <!-- Delete: Only for Super Admin AND Target != 1 -->
                                     @if(auth()->id() == 1 && $admin->user_id != 1)
                                        <form action="{{ route('admin.users.destroy', $admin->user_id) }}" method="POST" id="delete-admin-form-{{ $admin->user_id }}">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDeleteUser('{{ $admin->user_id }}', '{{ $admin->name }}')" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-lg transition-colors" title="Hapus Admin">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                     @endif
                                </div>
                            </td>
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
                    <!-- Status Searchable Dropdown -->
                    <div x-data="{
                        open: false,
                        selectedId: '{{ request('status') }}',
                        items: [
                            {id: 'active', name: 'Active'},
                            {id: 'suspended', name: 'Suspended'}
                        ],
                        get selectedName() {
                            const item = this.items.find(i => i.id == this.selectedId);
                            return item ? item.name : 'Semua Status';
                        }
                    }" class="relative w-full md:w-40" @click.outside="open = false">
                        <input type="hidden" name="status" :value="selectedId">
                        <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3 text-left flex justify-between items-center transition-all">
                            <span x-text="selectedName" class="truncate block pr-2"></span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" class="absolute z-50 w-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden flex flex-col" x-cloak>
                            <div @click="selectedId = ''; open = false" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">Semua Status</div>
                            <template x-for="item in items" :key="item.id">
                                <div @click="selectedId = item.id; open = false" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer" :class="{'bg-indigo-50 font-bold text-indigo-700': selectedId == item.id}" x-text="item.name"></div>
                            </template>
                        </div>
                    </div>

                    <!-- Role Searchable Dropdown -->
                     <div x-data="{
                        open: false,
                        selectedId: '{{ request('role') }}',
                        items: [
                            {id: 'buyer', name: 'Buyer'},
                            {id: 'seller', name: 'Seller'}
                        ],
                        get selectedName() {
                            const item = this.items.find(i => i.id == this.selectedId);
                            return item ? item.name : 'Semua Role';
                        }
                    }" class="relative w-full md:w-40" @click.outside="open = false">
                        <input type="hidden" name="role" :value="selectedId">
                        <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3 text-left flex justify-between items-center transition-all">
                            <span x-text="selectedName" class="truncate block pr-2"></span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" class="absolute z-50 w-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden flex flex-col" x-cloak>
                            <div @click="selectedId = ''; open = false" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">Semua Role</div>
                            <template x-for="item in items" :key="item.id">
                                <div @click="selectedId = item.id; open = false" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer" :class="{'bg-indigo-50 font-bold text-indigo-700': selectedId == item.id}" x-text="item.name"></div>
                            </template>
                        </div>
                    </div>

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
                                        <!-- View Detail (Always Visible) -->
                                        <a href="{{ route('admin.users.show', $user->user_id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border border-transparent hover:border-indigo-100" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        @if($user->role === 'admin')
                                            <!-- Logic KHUSUS Admin -->
                                            @php
                                                $currentId = auth()->id();
                                                $targetId = $user->user_id;
                                                $isSuperAdmin = $currentId == 1;
                                                $isSelf = $currentId == $targetId;
                                            @endphp
                                            
                                            <!-- Edit: Only for Super Admin OR Self -->
                                            @if($isSuperAdmin || $isSelf)
                                                <a href="{{ route('admin.users.edit', $user->user_id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border border-transparent hover:border-blue-100" title="Edit Data">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </a>
                                            @endif

                                            <!-- Other Actions: Only for Super Admin AND Target is NOT Self -->
                                            @if($isSuperAdmin && !$isSelf)
                                                 <!-- Reset Password -->
                                                <form action="{{ route('admin.users.send_reset_link', $user->user_id) }}" method="POST" id="reset-form-{{ $user->user_id }}">
                                                    @csrf
                                                    <button type="button" onclick="confirmResetPassword('{{ $user->user_id }}', '{{ $user->name }}')" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors border border-transparent hover:border-yellow-100" title="Kirim Reset Password Link">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                                    </button>
                                                </form>

                                                <!-- Status Toggle -->
                                                <form action="{{ route('admin.users.toggle_status', $user->user_id) }}" method="POST" id="status-form-{{ $user->user_id }}">
                                                    @csrf @method('PATCH')
                                                    @if($user->status === 'suspended')
                                                        <button type="button" onclick="confirmAction('{{ $user->user_id }}', 'aktifkan')" class="text-green-600 hover:bg-green-50 p-2 rounded-lg transition-colors" title="Aktifkan">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        </button>
                                                    @else
                                                        <button type="button" onclick="confirmAction('{{ $user->user_id }}', 'suspend')" class="text-orange-600 hover:bg-orange-50 p-2 rounded-lg transition-colors" title="Suspend">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                        </button>
                                                    @endif
                                                </form>

                                                <!-- Delete -->
                                                <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" id="delete-form-{{ $user->user_id }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDeleteUser('{{ $user->user_id }}', '{{ $user->name }}')" class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition-colors" title="Hapus Permanen">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif

                                        @else
                                            <!-- Logic STANDARD untuk Buyer/Seller -->

                                            <!-- Reset Link -->
                                            <form action="{{ route('admin.users.send_reset_link', $user->user_id) }}" method="POST" id="reset-form-{{ $user->user_id }}">
                                                @csrf
                                                <button type="button" onclick="confirmResetPassword('{{ $user->user_id }}', '{{ $user->name }}')" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors border border-transparent hover:border-yellow-100" title="Kirim Reset Password Link">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                                </button>
                                            </form>

                                            <!-- Toggle Status -->
                                            <form action="{{ route('admin.users.toggle_status', $user->user_id) }}" method="POST" id="status-form-{{ $user->user_id }}">
                                                @csrf @method('PATCH')
                                                @if($user->status === 'suspended')
                                                    <button type="button" onclick="confirmAction('{{ $user->user_id }}', 'aktifkan')" class="text-green-600 hover:bg-green-50 p-2 rounded-lg transition-colors" title="Aktifkan">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                @else
                                                    <button type="button" onclick="confirmAction('{{ $user->user_id }}', 'suspend')" class="text-orange-600 hover:bg-orange-50 p-2 rounded-lg transition-colors" title="Suspend">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                    </button>
                                                @endif
                                            </form>

                                            <!-- Delete User (Available for all admins targeting Buyer/Seller) -->
                                            <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" id="delete-form-{{ $user->user_id }}">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDeleteUser('{{ $user->user_id }}', '{{ $user->name }}')" class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition-colors" title="Hapus Permanen">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @endif
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

    <!-- Stats Grid with Red Hover -->
    <style>
        .hover-red-b:hover {
            border-color: #EC1C25 !important;
        }
    </style>

</div>

<script>
    // Custom SWAL for Reset Password
    function confirmResetPassword(userId, userName) {
        Swal.fire({
            title: 'Kirim Link Reset?',
            text: "Kirim link reset password via WhatsApp ke " + userName + "?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#F59E0B', // Yellow/Orange
            cancelButtonColor: '#E5E7EB',
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: '<span class="text-gray-800 font-medium">Batal</span>',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-4 py-2 font-bold shadow-lg',
                cancelButton: 'rounded-xl px-4 py-2 opacity-80 hover:opacity-100'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('reset-form-' + userId).submit();
            }
        });
    }

    // Custom SWAL for Delete User
    function confirmDeleteUser(userId, userName) {
        Swal.fire({
            title: 'HAPUS PENGGUNA?',
            html: "Anda akan menghapus pengguna <b>" + userName + "</b> beserta <b class='text-red-600'>SELURUH POSTINGAN/PRODUK</b> mereka.<br>Data tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EC1C25', // Red
            cancelButtonColor: '#E5E7EB',
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: '<span class="text-gray-800 font-medium">Batal</span>',
            reverseButtons: true, // Danger action on right usually, but standard left confirm is fine if color is distinct
            customClass: {
                popup: 'rounded-2xl border-2 border-red-500',
                confirmButton: 'rounded-xl px-4 py-2 font-bold shadow-lg shadow-red-500/30',
                cancelButton: 'rounded-xl px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + userId).submit();
            }
        });
    }

    // Konfirmasi Aksi SweetAlert (Toggle Status)
    function confirmAction(userId, action) {
        let titleText = action === 'suspend' ? 'Suspend Akun Pengguna?' : 'Aktifkan Kembali Akun?';
        let confirmText = action === 'suspend' ? 'Ya, Suspend!' : 'Ya, Aktifkan!';
        let btnColor = action === 'suspend' ? '#EF4444' : '#10B981'; 
        let iconType = action === 'suspend' ? 'warning' : 'info'; // Use info/success for activate

        Swal.fire({
            title: titleText,
            text: "Pastikan tindakan ini sudah sesuai prosedur.",
            icon: iconType,
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
