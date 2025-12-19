@extends('layouts.admin')

@section('content')
<!-- Tambahkan CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
<style>
    /* Custom Style untuk DataTables agar serasi dengan Flowbite */
    .dataTables_wrapper .dataTables_length select {
        padding-right: 2rem;
        background-color: #f9fafb;
        border-color: #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        background-color: #f9fafb;
        border-color: #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        margin-left: 0.5rem;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid #e5e7eb;
    }
    /* Styling Action Buttons */
    .action-btn {
        transition: all 0.2s;
    }
    .action-btn:hover {
        transform: translateY(-2px);
    }
</style>

<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Pengguna</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola akses, peran, dan status akun pengguna di platform.</p>
            </div>
            <!-- Tombol Tambah Admin -->
            <button data-modal-target="add-admin-modal" data-modal-toggle="add-admin-modal" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center gap-2 shadow-md transition-all transform hover:-translate-y-0.5" type="button">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Admin
            </button>
        </div>

        <!-- Statistik Mini Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Total Member</div>
                    <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['total_users'] }}</div>
            </div>
            <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Administrator</div>
                    <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['total_admins'] }}</div>
            </div>
            <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-semibold text-green-600 uppercase tracking-wide">Akun Aktif</div>
                    <div class="p-1.5 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['active_users'] }}</div>
            </div>
            <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-semibold text-red-600 uppercase tracking-wide">Disuspend</div>
                    <div class="p-1.5 bg-red-50 text-red-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['suspended_users'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col space-y-8 p-4">

    <!-- TABEL 1: ADMINISTRATOR -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex items-center gap-3">
            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">Daftar Administrator</h3>
                <p class="text-xs text-gray-500">Pengguna dengan akses penuh ke sistem.</p>
            </div>
        </div>
        <div class="overflow-x-auto p-4">
            <table id="adminTable" class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 rounded-lg">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Admin</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Username</th>
                        <th class="px-4 py-3 rounded-r-lg">Bergabung</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900 flex items-center gap-3">
                            <div class="flex-shrink-0 h-9 w-9">
                                <img class="h-9 w-9 rounded-full border border-gray-200 object-cover" src="{{ $admin->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($admin->name).'&background=3730a3&color=fff' }}" alt="">
                            </div>
                            <span class="font-semibold">{{ $admin->name }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $admin->email }}</td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs bg-gray-100 px-2 py-1 rounded w-fit">{{ $admin->username }}</td>
                        <td class="px-4 py-3">{{ $admin->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABEL 2: PENGGUNA TERDAFTAR -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Daftar Pengguna</h3>
                    <p class="text-xs text-gray-500">Buyer dan Seller yang terdaftar di platform.</p>
                </div>
            </div>

            <!-- Filter Status Custom -->
            <div class="flex items-center gap-2">
                <label for="statusFilter" class="text-sm font-medium text-gray-700">Status:</label>
                <select id="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    <option value="">Semua</option>
                    <option value="Active">Active</option>
                    <option value="Suspended">Suspended</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto p-4">
            <table id="usersTable" class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 rounded-lg">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Pengguna</th>
                        <th class="px-4 py-3">Kontak</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Bergabung</th>
                        <th class="px-4 py-3 text-center rounded-r-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10 relative">
                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-200" src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" alt="">
                                    @if($user->status === 'active')
                                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 border-2 border-white rounded-full"></span>
                                    @else
                                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                                    @endif
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                                    <span class="text-xs text-gray-400">@ {{ $user->username }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="text-gray-600">{{ $user->email }}</span>
                                @if($user->profile && $user->profile->phone)
                                    <span class="text-xs text-gray-400">{{ $user->profile->phone }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->role === 'seller')
                                <span class="bg-purple-100 text-purple-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-purple-200 uppercase tracking-wide">
                                    Seller
                                </span>
                            @else
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-blue-200 uppercase tracking-wide">
                                    Buyer
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($user->status === 'active')
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-green-200 uppercase tracking-wide inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Active
                                </span>
                            @elseif($user->status === 'suspended')
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-red-200 uppercase tracking-wide inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Suspended
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-gray-200 uppercase tracking-wide">
                                    {{ ucfirst($user->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.users.toggle_status', $user->user_id) }}" method="POST" id="status-form-{{ $user->user_id }}">
                                @csrf
                                @method('PATCH')

                                @if($user->status === 'suspended')
                                    <!-- Tombol Aktifkan (Hijau) -->
                                    <button type="button" onclick="confirmAction('{{ $user->user_id }}', 'aktifkan')" class="action-btn text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm p-2 transition-colors shadow-sm" title="Aktifkan Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                @else
                                    <!-- Tombol Suspend (Merah) -->
                                    <button type="button" onclick="confirmAction('{{ $user->user_id }}', 'suspend')" class="action-btn text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm p-2 transition-colors shadow-sm" title="Suspend Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div id="add-admin-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-gray-900/50">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-xl shadow-2xl overflow-hidden dark:bg-gray-700">
            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b bg-gray-50 dark:border-gray-600">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Tambah Administrator Baru
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="add-admin-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Body -->
            <form action="{{ route('admin.users.store_admin') }}" method="POST" class="p-6">
                @csrf
                <div class="grid gap-5 mb-6">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="John Doe" required>
                    </div>
                    <div>
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full ps-10 p-2.5" placeholder="admin_user" required>
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full ps-10 p-2.5" placeholder="admin@example.com" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="••••••••" required>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end border-t pt-4 border-gray-100">
                    <button type="submit" class="text-white inline-flex items-center bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-6 py-2.5 text-center shadow-lg transition-transform transform hover:-translate-y-0.5">
                        <svg class="me-2 -ms-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                        Simpan Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Init DataTable untuk Admin (simple)
        $('#adminTable').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "language": { "emptyTable": "Tidak ada data administrator" }
        });

        // Init DataTable untuk Users (lengkap)
        var table = $('#usersTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "dom": '<"flex flex-col sm:flex-row justify-between items-center pb-4 space-y-2 sm:space-y-0"<"flex items-center gap-2"l><"flex items-center gap-2"f>>rt<"flex flex-col sm:flex-row justify-between items-center pt-4"<"text-sm text-gray-500"i><"flex justify-center"p>>',
            "language": {
                "search": "",
                "searchPlaceholder": "Cari nama, email...",
                "lengthMenu": "Tampilkan _MENU_",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "paginate": {
                    "first": "<<",
                    "last": ">>",
                    "next": ">",
                    "previous": "<"
                },
                "zeroRecords": "Tidak ada pengguna ditemukan",
                "infoEmpty": "Menampilkan 0 data",
                "infoFiltered": "(dari _MAX_ total data)"
            },
            "columnDefs": [
                { "orderable": false, "targets": 5 } // Kolom Aksi tidak bisa di-sort
            ]
        });

        // Custom Filter Status Logic
        $('#statusFilter').on('change', function() {
            var status = $(this).val();
            table.column(3).search(status).draw(); // Kolom ke-3 (index 3) adalah kolom Status
        });
    });

    // Konfirmasi Aksi SweetAlert
    function confirmAction(userId, action) {
        let titleText = action === 'suspend' ? 'Suspend Akun Pengguna?' : 'Aktifkan Kembali Akun?';
        let confirmText = action === 'suspend' ? 'Ya, Suspend!' : 'Ya, Aktifkan!';
        let btnColor = action === 'suspend' ? '#d33' : '#10B981';

        Swal.fire({
            title: titleText,
            text: "Pastikan tindakan ini sudah sesuai prosedur.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#3085d6',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('status-form-' + userId).submit();
            }
        });
    }
</script>
@endsection
