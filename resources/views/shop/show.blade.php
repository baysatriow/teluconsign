@extends('layouts.app')

@section('content')
<!-- Modern Shop Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto">
        <!-- Banner Area -->
        <div class="h-48 md:h-64 w-full bg-gradient-to-r from-gray-900 via-gray-800 to-black relative overflow-hidden group">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-red-600/20 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
            
            <!-- Default Banner / Real Banner if available -->
            <div class="absolute inset-0 bg-cover bg-center opacity-60 mix-blend-overlay group-hover:scale-105 transition-transform duration-700" 
                 style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2070&auto=format&fit=crop');">
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
        </div>

        <!-- Shop Info Section -->
        <div class="px-4 sm:px-6 lg:px-8 pb-8 relative">
            <div class="flex flex-col md:flex-row items-end -mt-16 gap-6">
                <!-- Profile Image -->
                <div class="relative z-10">
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white shadow-xl overflow-hidden bg-white">
                        <img src="{{ $seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($seller->name).'&background=111827&color=fff' }}" 
                             class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" 
                             alt="{{ $seller->name }}">
                    </div>
                    @if($seller->role == 'seller')
                    <div class="absolute bottom-2 right-2 bg-blue-500 text-white p-1.5 rounded-full border-2 border-white shadow-md" title="Official Seller">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    @endif
                </div>

                <!-- Main Info -->
                <div class="flex-grow pb-2 w-full text-center md:text-left">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center justify-center md:justify-start gap-2">
                                {{ $seller->name }}
                            </h1>
                            <p class="text-sm text-gray-500 mt-1 font-medium flex items-center justify-center md:justify-start gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $seller->addresses->first()->city ?? ($seller->address->city ?? 'Lokasi Tidak Diketahui') }}
                                <span class="mx-1 text-gray-300">|</span>
                                Bergabung {{ $seller->created_at->translatedFormat('F Y') }}
                            </p>
                        </div>

                        <!-- Stats Cards -->
                        <div class="flex items-center justify-center md:justify-end gap-3 sm:gap-6">
                            <div class="text-center px-4 py-2 bg-gray-50 rounded-xl border border-gray-100 min-w-[80px]">
                                <span class="block text-xl font-bold text-gray-900">{{ $products->total() }}</span>
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Produk</span>
                            </div>
                            <div class="text-center px-4 py-2 bg-gray-50 rounded-xl border border-gray-100 min-w-[80px]">
                                <span class="block text-xl font-bold text-gray-900">{{ $totalSales ?? 0 }}</span>
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Terjual</span>
                            </div>
                            <div class="text-center px-4 py-2 bg-gray-50 rounded-xl border border-gray-100 min-w-[80px]">
                                <span class="flex items-center justify-center gap-1 text-xl font-bold text-gray-900">
                                    {{ number_format($rating ?? 0, 1) }} 
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </span>
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Filter Bar (Optional for future) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Etalase Toko</h2>
            <p class="text-sm text-gray-500 mt-1">Temukan produk terbaik dari {{ $seller->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative group">
                <input type="text" placeholder="Cari di toko..." class="pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-full text-sm focus:ring-2 focus:ring-[#EC1C25] w-full sm:w-64 transition-shadow shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3 group-hover:text-[#EC1C25] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="bg-gray-50 rounded-3xl p-16 text-center border-2 border-dashed border-gray-200">
            <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Produk</h3>
            <p class="text-gray-500 max-w-md mx-auto">Toko ini belum menambahkan produk ke etalase mereka. Cek kembali nanti ya!</p>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @foreach($products as $product)
            <div class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col h-full transform hover:-translate-y-1">
                <!-- Image -->
                <div class="relative w-full pt-[100%] bg-gray-100 overflow-hidden">
                    <img src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/400x300?text=No+Image' }}" 
                         alt="{{ $product->title }}" 
                         class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    
                    <!-- Badge Kondisi -->
                    <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-gray-900 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm border border-gray-100">
                        {{ $product->condition == 'new' ? 'BARU' : 'BEKAS' }}
                    </span>
                    
                    <!-- Overlay -->
                    <a href="{{ route('product.show', $product->slug ?? $product->product_id) }}" class="absolute inset-0 z-10"></a>
                </div>

                <!-- Content -->
                <div class="p-4 flex flex-col flex-grow">
                    <!-- Category Pill -->
                    <a href="{{ route('search.index', ['category' => $product->category_id]) }}" class="inline-block w-fit mb-2 z-20 relative">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider hover:text-[#EC1C25] transition-colors">
                            {{ $product->category->name ?? 'Umum' }}
                        </span>
                    </a>

                    <!-- Title -->
                    <h5 class="text-sm font-bold text-gray-900 line-clamp-2 leading-relaxed group-hover:text-[#EC1C25] transition-colors mb-2 min-h-[2.5em]">
                        {{ $product->title }}
                    </h5>

                    <!-- Price -->
                    <p class="text-lg font-extrabold text-[#EC1C25] mb-4">
                        Rp{{ number_format($product->price, 0, ',', '.') }}
                    </p>

                    <!-- Footer: Seller & Location (Consistent with Home) -->
                    <div class="mt-auto pt-3 border-t border-gray-50 space-y-2">
                        <!-- Seller -->
                        <div class="flex items-center gap-2 text-xs">
                            <img src="{{ $seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($seller->name).'&background=f3f4f6&color=6b7280' }}" 
                                 class="w-5 h-5 rounded-full object-cover border border-gray-100 flex-shrink-0">
                            <span class="truncate font-medium text-gray-700">{{ $seller->name }}</span>
                        </div>
                        
                        <!-- Location Badge -->
                        <div class="flex items-center gap-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-gray-500 font-medium">{{ $seller->addresses->first()->city ?? ($seller->address->city ?? 'Indonesia') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-12 flex justify-center">
            {{ $products->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection
