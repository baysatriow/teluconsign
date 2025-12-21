@extends('layouts.admin')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <nav class="flex mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs md:text-sm">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-900 transition-colors">Dashboard</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                        <a href="{{ route('admin.users') }}" class="text-gray-500 hover:text-gray-900 transition-colors">Pengguna</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                        <span class="text-gray-900 font-medium">Detail Pengguna</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Profil Pengguna</h1>
    </div>
    
    <div class="flex items-center gap-3">
        <form action="{{ route('admin.users.toggle_status', $user->user_id) }}" method="POST" id="status-toggle-form">
            @csrf @method('PATCH')
            @if($user->status === 'suspended')
                <button type="button" onclick="confirmStatusChange('aktifkan')" class="flex items-center gap-2 text-white bg-green-600 hover:bg-green-700 font-bold rounded-xl text-sm px-5 py-2.5 shadow-lg shadow-green-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Aktifkan Akun
                </button>
            @else
                <button type="button" onclick="confirmStatusChange('suspend')" class="flex items-center gap-2 text-white bg-red-600 hover:bg-red-700 font-bold rounded-xl text-sm px-5 py-2.5 shadow-lg shadow-red-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Suspend Akun
                </button>
            @endif
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Identity -->
    <div class="lg:col-span-1 space-y-8">
        <!-- Main Profile Card -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
             <div class="h-32 bg-gray-100 relative">
                <div class="absolute inset-0 bg-gradient-to-r from-gray-200 to-gray-50 opacity-50"></div>
             </div>
             <div class="px-6 pb-6 relative">
                 <div class="relative -mt-12 mb-4 flex justify-between items-end">
                     <img class="w-24 h-24 rounded-2xl border-4 border-white shadow-md bg-white object-cover" src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" alt="">
                     <div class="mb-1">
                        @if($user->status === 'active')
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                            </span>
                        @elseif($user->status === 'suspended')
                            <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Suspended
                            </span>
                        @else
                            <span class="bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1 rounded-full">
                                {{ ucfirst($user->status) }}
                            </span>
                        @endif
                     </div>
                 </div>
                 
                 <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                 <p class="text-sm text-gray-500 font-mono mb-4">{{ '@' . $user->username }}</p>

                 <div class="space-y-3 pt-4 border-t border-gray-100">
                     <div class="flex items-center gap-3 text-sm">
                         <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                         </div>
                         <span class="text-gray-600">{{ $user->email }}</span>
                     </div>
                     <div class="flex items-center gap-3 text-sm">
                         <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                         </div>
                         <span class="text-gray-600">{{ $user->profile->phone ?? '-' }}</span>
                     </div>
                     <div class="flex items-center gap-3 text-sm">
                         <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                         </div>
                         <span class="text-gray-600">Bergabung {{ $user->created_at->format('d M Y') }}</span>
                     </div>
                 </div>
             </div>
        </div>

        <!-- Address Card -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Alamat Utama</h3>
            @if($user->addresses->where('is_primary', true)->first())
                @php $addr = $user->addresses->where('is_primary', true)->first(); @endphp
                <div class="text-sm text-gray-600 leading-relaxed">
                    <p class="font-bold text-gray-900 mb-1">{{ $addr->contact_name }} <span class="text-gray-400 font-normal">({{ $addr->contact_phone }})</span></p>
                    <p>{{ $addr->street }}</p>
                    <p>{{ $addr->city ?? '-' }}, {{ $addr->province ?? '-' }} {{ $addr->postal_code }}</p>
                </div>
            @else
                 <div class="text-sm text-gray-400 italic">Belum ada alamat utama yang diatur.</div>
            @endif
            
            <div class="mt-4 pt-4 border-t border-gray-100">
                <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Semua Alamat ({{ $user->addresses->count() }})</h4>
                <div class="max-h-40 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                    @foreach($user->addresses as $address)
                        <div class="text-xs bg-gray-50 p-2 rounded-lg text-gray-600">
                             {{ $address->street }}, {{ $address->city }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Stats & Tables -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
             <div class="bg-white p-4 rounded-2xl shadow-soft border border-gray-100">
                 <div class="text-xs text-gray-400 uppercase font-bold mb-1">Barang Dijual</div>
                 <div class="text-2xl font-bold text-gray-900">{{ $products->count() }}</div>
             </div>
             <div class="bg-white p-4 rounded-2xl shadow-soft border border-gray-100">
                 <div class="text-xs text-gray-400 uppercase font-bold mb-1">Transaksi Beli</div>
                 <div class="text-2xl font-bold text-gray-900">{{ $orders->where('buyer_id', $user->user_id)->count() }}</div>
             </div>
             <div class="bg-white p-4 rounded-2xl shadow-soft border border-gray-100">
                 <div class="text-xs text-gray-400 uppercase font-bold mb-1">Transaksi Jual</div>
                 <div class="text-2xl font-bold text-gray-900">{{ $orders->where('seller_id', $user->user_id)->count() }}</div>
             </div>
             <div class="bg-white p-4 rounded-2xl shadow-soft border border-gray-100">
                 <div class="text-xs text-blue-400 uppercase font-bold mb-1">Role</div>
                 <div class="text-xl font-bold text-gray-900 uppercase">{{ $user->role }}</div>
             </div>
        </div>

        <!-- Products List -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
             <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                 <h3 class="font-bold text-gray-900 text-lg">Daftar Barang Dijual</h3>
                 <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $products->count() }} Item</span>
             </div>
             <div class="overflow-x-auto">
                 <table class="w-full text-sm text-left text-gray-500">
                     <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                         <tr>
                             <th class="px-6 py-3">Produk</th>
                             <th class="px-6 py-3">Kategori</th>
                             <th class="px-6 py-3">Harga</th>
                             <th class="px-6 py-3">Status</th>
                             <th class="px-6 py-3 text-center">Aksi</th>
                         </tr>
                     </thead>
                     <tbody class="divide-y divide-gray-100">
                         @forelse($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <div class="flex items-center gap-3">
                                        <img class="w-10 h-10 rounded-lg object-cover bg-gray-100" src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/100' }}">
                                        {{ Str::limit($product->title, 30) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $product->category->name ?? '-' }}</td>
                                <td class="px-6 py-4 font-mono text-xs">Rp{{ number_format($product->price,0,',','.') }}</td>
                                <td class="px-6 py-4">
                                     @if($product->status == \App\Enums\ProductStatus::Active)
                                         <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">Active</span>
                                     @elseif($product->status == \App\Enums\ProductStatus::Suspended)
                                         <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-bold">Suspended</span>
                                     @else
                                         <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $product->status->value ?? '-' }}</span>
                                     @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.products.show', $product->product_id) }}" class="text-indigo-600 hover:underline">Lihat</a>
                                </td>
                            </tr>
                         @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada produk yang dijual.</td>
                            </tr>
                         @endforelse
                     </tbody>
                 </table>
             </div>
        </div>
        
    </div>
</div>

<script>
    function confirmStatusChange(action) {
        let isSuspend = action === 'suspend';
        let titleText = isSuspend ? 'Suspend Akun Pengguna?' : 'Aktifkan Kembali Akun?';
        let confirmText = isSuspend ? 'Ya, Suspend!' : 'Ya, Aktifkan!';
        let btnColor = isSuspend ? '#EF4444' : '#10B981';

        Swal.fire({
            title: titleText,
            text: "Konfirmasi tindakan perubahan status akun ini.",
            icon: isSuspend ? 'warning' : 'info',
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#E5E7EB',
            confirmButtonText: confirmText,
            cancelButtonText: '<span class="text-gray-800">Batal</span>',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-5 py-2.5 font-bold shadow-lg',
                cancelButton: 'rounded-xl px-5 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('status-toggle-form').submit();
            }
        });
    }
</script>
@endsection
