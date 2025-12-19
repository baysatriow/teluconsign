@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8">

    <!-- Header Dashboard -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard Toko</h1>
            <p class="text-gray-500 mt-1">Pantau performa bisnis dan kelola inventaris produk Anda.</p>
        </div>
        <a href="{{ route('shop.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-[#EC1C25] rounded-lg hover:bg-[#c4161e] focus:ring-4 focus:ring-red-300 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Produk
        </a>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Pendapatan -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Pendapatan Bersih</h3>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="flex items-baseline">
                <span class="text-2xl font-bold text-gray-900">Rp{{ number_format($stats['total_sales'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Pesanan Baru -->
        <a href="{{ route('shop.orders') }}" class="group bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all relative overflow-hidden">
            <div class="absolute top-0 right-0 w-2 h-2 bg-blue-500 rounded-bl-full"></div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500 group-hover:text-blue-600 transition-colors uppercase tracking-wider">Pesanan Baru</h3>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['new'] }}</span>
                <span class="text-xs text-blue-600 font-medium bg-blue-50 px-2 py-0.5 rounded-full">Perlu Proses</span>
            </div>
        </a>

        <!-- Dikirim -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Dalam Pengiriman</h3>
                <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                </div>
            </div>
            <span class="text-3xl font-bold text-gray-900">{{ $stats['shipping'] }}</span>
        </div>

        <!-- Selesai -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Pesanan Selesai</h3>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <span class="text-3xl font-bold text-gray-900">{{ $stats['completed'] }}</span>
        </div>
    </div>

    <!-- Tabs Manajemen Produk -->
    <!-- Manajemen Produk (Tabs & Table) -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mt-8">

        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 bg-gray-50/50 px-4">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="productTab" data-tabs-toggle="#productTabContent" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all data-[state=active]:text-[#EC1C25] data-[state=active]:border-[#EC1C25] group" id="active-tab" data-tabs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="false">
                        Produk Aktif
                        <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-0.5 rounded-full group-data-[state=active]:bg-red-50 group-data-[state=active]:text-[#EC1C25] transition-colors">{{ $products->total() }}</span>
                    </button>
                </li>
                <li class="me-2" role="presentation">
                    <button class="inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all" id="draft-tab" data-tabs-target="#draft" type="button" role="tab" aria-controls="draft" aria-selected="false">
                        Draft / Arsip
                        <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-0.5 rounded-full">{{ $drafts->total() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div id="productTabContent">

            <!-- TAB 1: PRODUK AKTIF -->
            <div class="hidden" id="active" role="tabpanel" aria-labelledby="active-tab">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Produk</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Harga</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Stok</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Status</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($products as $prod)
                            <tr class="bg-white hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0 bg-gray-100 relative">
                                            <img class="w-full h-full object-cover" src="{{ $prod->main_image ? asset('storage/'.$prod->main_image) : 'https://placehold.co/100' }}" alt="{{ $prod->title }}">
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 line-clamp-1 group-hover:text-[#EC1C25] transition-colors">{{ $prod->title }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5">{{ $prod->category->name ?? 'Uncategorized' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    Rp{{ number_format($prod->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($prod->stock <= 0)
                                        <span class="bg-red-50 text-red-600 text-xs font-medium px-2.5 py-0.5 rounded-full border border-red-100">Habis</span>
                                    @else
                                        <span class="text-gray-900">{{ $prod->stock }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-{{ $prod->status->color() }}-100 text-{{ $prod->status->color() }}-800 text-xs font-medium px-2.5 py-0.5 rounded border border-{{ $prod->status->color() }}-200">
                                        {{ $prod->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit Icon -->
                                        <a href="{{ route('shop.products.edit', $prod->product_id) }}" class="p-2 text-blue-600 hover:text-white border border-blue-200 hover:bg-blue-600 rounded-lg transition-all" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                        <!-- Delete Icon -->
                                        <button onclick="confirmDelete('{{ $prod->product_id }}', '{{ addslashes($prod->title) }}')" class="p-2 text-red-600 hover:text-white border border-red-200 hover:bg-red-600 rounded-lg transition-all" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        <!-- Hidden Delete Form -->
                                        <form id="delete-form-{{ $prod->product_id }}" action="{{ route('shop.products.delete', $prod->product_id) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-gray-500 font-medium">Belum ada produk aktif.</p>
                                    <a href="{{ route('shop.products.create') }}" class="text-[#EC1C25] text-sm hover:underline mt-1 font-medium">Mulai jualan sekarang</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="p-4 border-t border-gray-100 flex justify-center">
                    {{ $products->appends(['draft_page' => $drafts->currentPage()])->links('pagination::tailwind') }}
                </div>
            </div>

            <!-- TAB 2: DRAFT / ARSIP -->
            <div class="hidden" id="draft" role="tabpanel" aria-labelledby="draft-tab">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Produk</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($drafts as $draft)
                            <tr class="bg-white hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0 bg-gray-100 relative">
                                            @if($draft->main_image)
                                                <img class="w-full h-full object-cover opacity-70 grayscale" src="{{ asset('storage/'.$draft->main_image) }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-gray-600">{{ $draft->title }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-{{ $draft->status->color() }}-100 text-{{ $draft->status->color() }}-800 text-xs font-medium px-2.5 py-0.5 rounded border border-{{ $draft->status->color() }}-200">
                                        {{ $draft->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit Icon -->
                                        <a href="{{ route('shop.products.edit', $draft->product_id) }}" class="p-2 text-blue-600 hover:text-white border border-blue-200 hover:bg-blue-600 rounded-lg transition-all" title="Edit / Terbitkan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                        <!-- Delete Icon -->
                                        <button onclick="confirmDelete('{{ $draft->product_id }}', '{{ addslashes($draft->title) }}')" class="p-2 text-red-600 hover:text-white border border-red-200 hover:bg-red-600 rounded-lg transition-all" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        <form id="delete-form-{{ $draft->product_id }}" action="{{ route('shop.products.delete', $draft->product_id) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada draft produk.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Links Draft -->
                <div class="p-4 border-t border-gray-100 flex justify-center">
                    {{ $drafts->appends(['active_page' => $products->currentPage()])->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, title) {
        Swal.fire({
            title: 'Hapus Produk?',
            text: `Anda akan menghapus "${title}". Pastikan produk ini tidak memiliki pesanan aktif.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
