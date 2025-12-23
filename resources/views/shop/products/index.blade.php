@extends('layouts.seller')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Produk</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola katalog produk jastip anda di sini.</p>
        </div>
        <a href="{{ route('shop.products.create') }}" class="group inline-flex items-center gap-2 px-5 py-2.5 bg-telu-red hover:bg-red-700 text-white font-medium rounded-xl shadow-lg shadow-red-500/30 transition-all hover:-translate-y-0.5">
            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Produk
        </a>
    </div>

    <!-- Stats Mini -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- TOTAL -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <!-- AKTIF -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-green-50 text-green-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Aktif</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['active'] }}</p>
            </div>
        </div>
        <!-- DRAFT -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-yellow-50 text-yellow-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Draft</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['draft'] }}</p>
            </div>
        </div>
        <!-- SUSPEND -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-red-50 text-red-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Suspended</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['suspended'] }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        
        <!-- Toolbar: Tabs + Search -->
        <div class="border-b border-gray-100 bg-gray-50/50 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            
            <!-- Tab Navigation (Scrollable) -->
            <nav class="flex space-x-1 bg-gray-100/50 p-1 rounded-xl overflow-x-auto min-w-0 max-w-full" aria-label="Tabs">
                @php
                    $tabs = [
                        'all' => ['label' => 'Semua', 'count' => $stats['total']],
                        'active' => ['label' => 'Aktif', 'count' => $stats['active']],
                        'draft' => ['label' => 'Draft', 'count' => $stats['draft']],
                        'suspended' => ['label' => 'Suspended', 'count' => $stats['suspended']],
                        'empty' => ['label' => 'Stok Habis', 'count' => $stats['empty']],
                    ];
                @endphp

                @foreach($tabs as $key => $data)
                <a href="{{ route('shop.products.index', ['tab' => $key, 'q' => request('q')]) }}" 
                   class="whitespace-nowrap flex items-center gap-2 py-2 px-3 rounded-lg text-sm font-medium transition-all {{ $tab == $key ? 'bg-white text-gray-900 shadow-sm ring-1 ring-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50' }}">
                   {{ $data['label'] }}
                   @if($key !== 'all')
                   <span class="{{ $tab == $key ? 'bg-gray-100 text-gray-900' : 'bg-gray-200 text-gray-600' }} py-0.5 px-2 rounded-md text-[10px] font-bold transition-colors">
                       {{ $data['count'] }}
                   </span>
                   @endif
                </a>
                @endforeach
            </nav>

            <!-- Search Bar -->
            <form action="{{ route('shop.products.index') }}" method="GET" class="w-full md:w-72 relative">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="search" name="q" value="{{ request('q') }}" 
                           class="block w-full p-2.5 pl-10 pr-20 text-sm text-gray-900 border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-0 focus:border-telu-red transition-colors" 
                           placeholder="Cari nama produk...">
                    <button type="submit" class="absolute right-1 top-1 bottom-1 px-4 bg-telu-red text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-colors">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Content -->
        <div class="relative overflow-x-auto min-h-[400px]">
            <table class="w-full text-sm text-left align-middle">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider w-[40%]">Produk</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Harga</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Stok</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $prod)
                    <tr class="bg-white hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl overflow-hidden border border-gray-100 flex-shrink-0 bg-gray-50 relative">
                                    <img class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500" 
                                         src="{{ $prod->main_image ? asset('storage/'.$prod->main_image) : 'https://placehold.co/100?text=No+Img' }}" 
                                         alt="{{ $prod->title }}">
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900 line-clamp-2 md:line-clamp-1 group-hover:text-telu-red transition-colors mb-1">{{ $prod->title }}</div>
                                    <div class="text-xs text-gray-500 bg-gray-100 inline-block px-2 py-0.5 rounded">{{ $prod->category->name ?? 'Uncategorized' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                            Rp{{ number_format($prod->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($prod->stock <= 0)
                                <span class="bg-red-50 text-red-600 text-xs font-bold px-2.5 py-1 rounded-full border border-red-100">Habis</span>
                            @else
                                <span class="text-gray-900 font-medium bg-gray-100 px-2.5 py-1 rounded-lg">{{ $prod->stock }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColor = 'gray';
                                if($prod->status->value == 'active') $statusColor = 'green';
                                if($prod->status->value == 'suspended') $statusColor = 'red';
                                if($prod->status->value == 'archived') $statusColor = 'yellow';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-{{ $statusColor }}-200 uppercase tracking-wide">
                                <span class="w-1.5 h-1.5 rounded-full bg-{{ $statusColor }}-500"></span>
                                {{ $prod->status->label() ?? $prod->status->value }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-center gap-2">
                                        @if($prod->status->value === 'suspended')
                                            <button type="button" 
                                                onclick="showSuspensionReason({{ json_encode($prod->suspension_reason) }})" 
                                                class="text-xs bg-red-100 text-red-600 px-3 py-1.5 rounded-lg font-medium border border-red-200 hover:bg-red-200 transition-colors">
                                                Detail
                                            </button>
                                            
                                            <button onclick="confirmDeleteProduct('{{ $prod->product_id }}', '{{ $prod->title }}')" 
                                                class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-all shadow-sm group">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @else
                                            <a href="{{ route('shop.products.edit', $prod) }}" 
                                                class="p-2 bg-blue-50 text-blue-600 rounded-lg border border-blue-200 hover:bg-blue-100 transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            
                                            <button onclick="confirmDeleteProduct('{{ $prod->product_id }}', '{{ $prod->title }}')" 
                                                class="p-2 bg-red-50 text-red-600 rounded-lg border border-red-200 hover:bg-red-100 transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                    <form id="delete-form-{{ $prod->product_id }}" action="{{ route('shop.products.delete', $prod->product_id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Tidak ada produk ditemukan</h3>
                                <p class="text-gray-400 text-sm max-w-xs mx-auto">Coba ubah kata kunci pencarian atau filter status produk anda.</p>
                                @if(request('q'))
                                <a href="{{ route('shop.products.index', ['tab' => $tab]) }}" class="mt-4 text-telu-red hover:text-red-700 text-sm font-medium hover:underline">
                                    Hapus Pencarian
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="p-4 border-t border-gray-100 flex justify-center bg-gray-50">
            {{ $products->links('pagination::tailwind') }}
        </div>
        @endif
        
    </div>
</div>

<script>
    function showSuspensionReason(reason) {
        Swal.fire({
            icon: 'error',
            title: 'Produk Disuspend',
            text: reason || 'Tidak ada alasan spesifik.',
            confirmButtonColor: '#EC1C25'
        });
    }

    function confirmDeleteProduct(id, name) {
        Swal.fire({
            title: 'Hapus Produk?',
            html: `Anda akan menghapus produk <b>"${name}"</b>.<br><br><span style="font-size: 13px; color: #ef4444; font-weight: 600;">Catatan: Produk ini juga akan dihapus dari semua keranjang belanja pengguna.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-form-' + id);
                if (form) {
                    form.submit();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Form tidak ditemukan. Silakan refresh halaman.',
                        confirmButtonColor: '#EC1C25'
                    });
                }
            }
        });
    }
</script>
@endsection
