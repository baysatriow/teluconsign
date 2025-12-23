@extends('layouts.admin')

@section('content')
<!-- Header & Breadcrumbs -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
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
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Profil Pengguna</h1>
    </div>
    
    <div class="flex items-center gap-3">
        <!-- Status Toggle -->
        <form action="{{ route('admin.users.toggle_status', $user->user_id) }}" method="POST" id="status-toggle-form">
            @csrf @method('PATCH')
            @if($user->status === 'suspended')
                <button type="button" onclick="confirmStatusChange('aktifkan')" class="flex items-center gap-2 text-white bg-green-600 hover:bg-green-700 font-bold rounded-2xl text-sm px-6 py-3 shadow-lg shadow-green-500/20 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Aktifkan Akun
                </button>
            @else
                <button type="button" onclick="confirmStatusChange('suspend')" class="flex items-center gap-2 text-white bg-red-600 hover:bg-red-700 font-bold rounded-2xl text-sm px-6 py-3 shadow-lg shadow-red-500/20 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Suspend Akun
                </button>
            @endif
        </form>

        <!-- Delete Button (Only for Allowed) -->
        @if(Auth::id() != $user->user_id && $user->user_id != 1 && ($user->role !== 'admin' || Auth::id() == 1))
            <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" id="delete-user-form">
                @csrf @method('DELETE')
                <button type="button" onclick="confirmDeleteUser()" class="flex items-center justify-center w-12 h-12 text-red-600 bg-red-50 hover:bg-red-100 border border-red-100 font-bold rounded-2xl transition-all hover:-translate-y-0.5" title="Hapus Pengguna Permanen">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Left Column: Identity & Contact -->
    <div class="lg:col-span-4 space-y-8">
        <!-- Main Profile Card -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
             <!-- Banner Image -->
             <div class="h-32 w-full bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80');">
                <div class="absolute inset-0 bg-black/20"></div>
             </div>
             
             <div class="px-6 pb-6 relative">
                 <div class="relative -mt-12 mb-4 flex justify-between items-end">
                     <img class="w-24 h-24 rounded-2xl border-4 border-white shadow-md bg-white object-cover" src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=EC1C25&color=fff' }}" alt="">
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
                 <p class="text-sm text-gray-500 font-mono mb-6">{{ '@' . $user->username }}</p>

                 <div class="space-y-4 pt-4 border-t border-gray-100">
                     <div class="flex items-center gap-3 text-sm">
                         <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                         </div>
                         <span class="text-gray-600 font-medium">{{ $user->email }}</span>
                     </div>
                     <div class="flex items-center gap-3 text-sm">
                         <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                         </div>
                         <span class="text-gray-600 font-medium">{{ $user->profile->phone ?? '-' }}</span>
                     </div>
                     <div class="flex items-center gap-3 text-sm">
                         <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                         </div>
                         <span class="text-gray-600 font-medium">Bergabung {{ $user->created_at->format('d M Y') }}</span>
                     </div>
                 </div>
             </div>
        </div>

        <!-- Address Card -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                Alamat Utama
            </h3>
            
            @php 
                $primaryAddress = $user->addresses->where('is_default', true)->first(); 
            @endphp

            @if($primaryAddress)
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="font-bold text-gray-900 text-sm mb-1">{{ $primaryAddress->contact_name ?? $user->name }}</p>
                    <p class="text-xs text-gray-500 font-mono mb-2">{{ $primaryAddress->phone ?? $user->profile->phone ?? '-' }}</p>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ $primaryAddress->detail_address }}<br>
                        {{ $primaryAddress->district }}, {{ $primaryAddress->city }}<br>
                        {{ $primaryAddress->province }} {{ $primaryAddress->postal_code }}
                    </p>
                </div>
            @else
                 <div class="text-sm text-gray-400 italic text-center py-4 bg-gray-50 rounded-xl border border-gray-100 border-dashed">
                    Belum ada alamat utama yang diatur.
                </div>
            @endif
            
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="text-xs font-bold text-red-600 hover:text-red-700 uppercase flex items-center justify-between w-full">
                        <span>Lihat Semua Alamat ({{ $user->addresses->count() }})</span>
                        <svg class="w-4 h-4 transform transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" class="mt-3 space-y-2 max-h-40 overflow-y-auto custom-scrollbar">
                        @foreach($user->addresses as $address)
                            <div class="text-xs bg-white border border-gray-100 p-3 rounded-xl text-gray-600 {{ $address->is_default ? 'ring-2 ring-red-500 bg-red-50' : '' }}">
                                 <span class="block font-bold truncate text-gray-800">{{ $address->label ?? 'Rumah' }}</span>
                                 <span class="block truncate mt-0.5">{{ $address->city }}, {{ $address->province }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Stats & Products Table -->
    <div class="lg:col-span-8 space-y-8">
        
        <!-- Summary Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
             <div class="bg-white p-5 rounded-2xl shadow-soft border border-gray-100 group hover:-translate-y-1 transition-transform hover-red-b">
                 <div class="text-xs text-gray-400 uppercase font-bold mb-2">Total Produk</div>
                 <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ $products->total() }}</div>
             </div>
             <div class="bg-white p-5 rounded-2xl shadow-soft border border-gray-100 group hover:-translate-y-1 transition-transform hover-red-b">
                 <div class="text-xs text-green-500 uppercase font-bold mb-2">Transaksi Beli</div>
                 <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ $buyCount ?? 0 }}</div>
             </div>
             <div class="bg-white p-5 rounded-2xl shadow-soft border border-gray-100 group hover:-translate-y-1 transition-transform hover-red-b">
                 <div class="text-xs text-blue-500 uppercase font-bold mb-2">Transaksi Jual</div>
                 <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ $sellCount ?? 0 }}</div>
             </div>
             <div class="bg-white p-5 rounded-2xl shadow-soft border border-gray-100 group hover:-translate-y-1 transition-transform hover-red-b">
                 <div class="text-xs text-gray-400 uppercase font-bold mb-2">Role</div>
                 <div class="text-xl font-bold text-gray-900 uppercase pt-1">{{ $user->role }}</div>
             </div>
        </div>

        <!-- Products List Section -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
             <!-- Filter Bar -->
             <div class="p-5 border-b border-gray-100 bg-gray-50/30">
                 <form action="{{ route('admin.users.show', $user->user_id) }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
                     <!-- Search -->
                     <div class="w-full relative">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                             <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                         </div>
                         <input type="text" name="q" value="{{ request('q') }}" class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 block w-full pl-10 p-3 transition-colors placeholder-gray-400" placeholder="Cari produk...">
                     </div>
                     
                     <!-- Category Searchable Dropdown -->
                     <div x-data="{
                        open: false,
                        search: '',
                        selectedId: '{{ request('category') }}',
                        items: [
                            @foreach($categories as $cat)
                                {id: '{{ $cat->category_id }}', name: '{{ addslashes($cat->name) }}'},
                            @endforeach
                        ],
                        get filteredItems() {
                            if (this.search === '') return this.items;
                            return this.items.filter(item => item.name.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        get selectedName() {
                            const item = this.items.find(i => i.id == this.selectedId);
                            return item ? item.name : 'Semua Kategori';
                        }
                    }" class="relative w-full md:w-64" @click.outside="open = false">
                        <input type="hidden" name="category" :value="selectedId">
                        
                        <button @click="open = !open" type="button" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 p-3 text-left flex justify-between items-center transition-all hover:bg-gray-50">
                            <span x-text="selectedName" class="truncate block pr-2 font-medium"></span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" class="absolute z-50 w-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden flex flex-col max-h-60" x-transition x-cloak>
                            <!-- Search Input Inside Dropdown -->
                            <div class="p-2 border-b border-gray-100 bg-gray-50">
                                <input type="text" x-model="search" class="w-full text-xs p-2 border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20" placeholder="Cari..." @click.stop>
                            </div>
                            
                            <div class="overflow-y-auto custom-scrollbar flex-1">
                                <div @click="selectedId = ''; open = false" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer border-b border-gray-50 font-medium">Semua Kategori</div>
                                <template x-for="item in filteredItems" :key="item.id">
                                    <div @click="selectedId = item.id; open = false" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer" :class="{'bg-red-50 font-bold text-red-600': selectedId == item.id}" x-text="item.name"></div>
                                </template>
                                <div x-show="filteredItems.length === 0" class="px-3 py-2 text-xs text-gray-400 italic text-center">Tidak ditemukan.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Dropdown -->
                    <div x-data="{
                        open: false,
                        selectedId: '{{ request('status') }}',
                        items: [
                            {id: 'active', name: 'Active'},
                            {id: 'suspended', name: 'Suspended'},
                            {id: 'sold', name: 'Sold'}
                        ],
                        get selectedName() {
                            const item = this.items.find(i => i.id == this.selectedId);
                            return item ? item.name : 'Semua Status';
                        }
                    }" class="relative w-full md:w-48" @click.outside="open = false">
                        <input type="hidden" name="status" :value="selectedId">
                        
                        <button @click="open = !open" type="button" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 p-3 text-left flex justify-between items-center transition-all hover:bg-gray-50">
                            <span x-text="selectedName" class="truncate block pr-2 font-medium"></span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" class="absolute z-50 w-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden flex flex-col" x-transition x-cloak>
                            <div @click="selectedId = ''; open = false" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer border-b border-gray-50 font-medium">Semua Status</div>
                            <template x-for="item in items" :key="item.id">
                                <div @click="selectedId = item.id; open = false" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer" :class="{'bg-red-50 font-bold text-red-600': selectedId == item.id}" x-text="item.name"></div>
                            </template>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <button type="submit" class="text-white bg-gray-900 hover:bg-black font-bold rounded-xl text-sm px-6 py-3 shadow-lg transition-transform hover:-translate-y-0.5">
                            Filter
                        </button>
                        @if(request()->has('q') || request()->has('category') || request()->has('status'))
                            <a href="{{ route('admin.users.show', $user->user_id) }}" class="text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 font-bold rounded-xl text-sm px-4 py-3 transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                 </form>
             </div>

             <!-- Table -->
             <div class="overflow-x-auto">
                 <table class="w-full text-sm text-left text-gray-500">
                     <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 border-b border-gray-100">
                         <tr>
                             <th class="px-6 py-4 font-bold tracking-wider">Produk</th>
                             <th class="px-6 py-4 font-bold tracking-wider">Kategori</th>
                             <th class="px-6 py-4 font-bold tracking-wider">Harga</th>
                             <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                             <th class="px-6 py-4 font-bold tracking-wider text-center">Detail</th>
                         </tr>
                     </thead>
                     <tbody class="divide-y divide-gray-100">
                         @forelse($products as $product)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex-shrink-0 border border-gray-200 overflow-hidden">
                                            <img class="w-full h-full object-cover" src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/100?text=Img' }}" alt="">
                                        </div>
                                        <div>
                                            <div class="font-bold line-clamp-1 text-gray-900" title="{{ $product->title }}">{{ Str::limit($product->title, 40) }}</div>
                                            <div class="text-xs text-gray-400 font-mono mt-0.5">ID: {{ $product->product_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $product->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-gray-700">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                     @if($product->status == \App\Enums\ProductStatus::Active)
                                         <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700">Active</span>
                                     @elseif($product->status == \App\Enums\ProductStatus::Suspended)
                                         <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">Suspended</span>
                                     @elseif($product->status == \App\Enums\ProductStatus::Sold)
                                         <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">Sold</span>
                                     @else
                                         <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-600">{{ strtoupper($product->status->value ?? $product->status) }}</span>
                                     @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.products.show', $product->product_id) }}" class="text-gray-400 hover:text-red-600 transition-colors">
                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </td>
                            </tr>
                         @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        <p>Tidak ada produk yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                         @endforelse
                     </tbody>
                 </table>
             </div>
             
             <!-- Pagination -->
             @if($products->hasPages())
             <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                 {{ $products->links() }}
             </div>
             @endif
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
            cancelButtonColor: '#F3F4F6', // Gray-100
            confirmButtonText: confirmText,
            cancelButtonText: '<span class="text-gray-800 font-bold">Batal</span>',
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

    function confirmDeleteUser() {
        Swal.fire({
            title: 'Hapus Pengguna Permanen?',
            text: "Tindakan ini tidak dapat dibatalkan. Seluruh data pengguna (produk, order, saldo) akan dihapus secara permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#F3F4F6',
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: '<span class="text-gray-800 font-bold">Batal</span>',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-5 py-2.5 font-bold shadow-lg',
                cancelButton: 'rounded-xl px-5 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-user-form').submit();
            }
        });
    }

</script>
@endsection
