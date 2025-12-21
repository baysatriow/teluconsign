@extends('layouts.seller')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Produk</h2>
            <p class="text-sm text-gray-500">Kelola katalog produk jastip anda di sini.</p>
        </div>
        <a href="{{ route('shop.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-telu-red hover:bg-red-700 text-white font-medium rounded-xl shadow-lg shadow-red-500/30 transition-all hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Produk
        </a>
    </div>

    <!-- Stats Mini -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card-premium p-4 flex items-center gap-3">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Total</p>
                <p class="text-lg font-bold text-gray-900">{{ $products->total() + $drafts->total() }}</p>
            </div>
        </div>
        <div class="card-premium p-4 flex items-center gap-3">
            <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Aktif</p>
                <p class="text-lg font-bold text-gray-900">{{ $products->total() }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card-premium overflow-hidden">
        
        <!-- Tab Navigation -->
        <div class="border-b border-gray-100 px-6 py-2 bg-gray-50/30 backdrop-blur-sm">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="productTab" data-tabs-toggle="#productTabContent" role="tablist">
                <li class="me-6" role="presentation">
                    <button class="inline-flex items-center gap-2 py-4 border-b-2 transition-all data-[state=active]:border-telu-red data-[state=active]:text-telu-red hover:text-gray-600 text-gray-500 border-transparent group" id="active-tab" data-tabs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="false">
                        Produk Aktif
                        <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded-full group-data-[state=active]:bg-red-50 group-data-[state=active]:text-telu-red transition-colors">{{ $products->total() }}</span>
                    </button>
                </li>
                <li class="me-6" role="presentation">
                    <button class="inline-flex items-center gap-2 py-4 border-b-2 transition-all data-[state=active]:border-telu-red data-[state=active]:text-telu-red hover:text-gray-600 text-gray-500 border-transparent" id="draft-tab" data-tabs-target="#draft" type="button" role="tab" aria-controls="draft" aria-selected="false">
                        Draft / Arsip
                        <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $drafts->total() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div id="productTabContent">
            <!-- TAB 1: PRODUK AKTIF -->
            <div class="hidden" id="active" role="tabpanel" aria-labelledby="active-tab">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Produk</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Harga</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Stok</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($products as $prod)
                            <tr class="bg-white hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden border border-gray-100 flex-shrink-0 bg-gray-50 relative group-hover:shadow-sm transition-all">
                                            <img class="w-full h-full object-cover" src="{{ $prod->main_image ? asset('storage/'.$prod->main_image) : 'https://placehold.co/100' }}" alt="{{ $prod->title }}">
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 line-clamp-1 group-hover:text-telu-red transition-colors">{{ $prod->title }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5">{{ $prod->category->name ?? 'Uncategorized' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    Rp{{ number_format($prod->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($prod->stock <= 0)
                                        <span class="bg-red-50 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full border border-red-100">Habis</span>
                                    @else
                                        <span class="text-gray-900 font-medium">{{ $prod->stock }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2 opacity-50 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('shop.products.edit', $prod->product_id) }}" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-100" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                        <button onclick="confirmDelete('delete-form-{{ $prod->product_id }}')" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-100" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        <form id="delete-form-{{ $prod->product_id }}" action="{{ route('shop.products.delete', $prod->product_id) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p>Belum ada produk aktif.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 flex justify-center">
                    {{ $products->appends(['draft_page' => $drafts->currentPage()])->links('pagination::tailwind') }}
                </div>
            </div>

            <!-- TAB 2: DRAFT / ARSIP -->
            <div class="hidden" id="draft" role="tabpanel" aria-labelledby="draft-tab">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Produk</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($drafts as $draft)
                            <tr class="bg-white hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden border border-gray-100 flex-shrink-0 bg-gray-50 relative">
                                            @if($draft->main_image)
                                                <img class="w-full h-full object-cover opacity-70 grayscale" src="{{ asset('storage/'.$draft->main_image) }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-gray-600 font-medium">{{ $draft->title }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-0.5 rounded border border-gray-200 uppercase">
                                        {{ $draft->status->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2 opacity-50 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('shop.products.edit', $draft->product_id) }}" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-100" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                        <button onclick="confirmDelete('delete-form-{{ $draft->product_id }}')" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-100" title="Hapus">
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
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada draft.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 flex justify-center">
                    {{ $drafts->appends(['active_page' => $products->currentPage()])->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
