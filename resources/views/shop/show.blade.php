@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8">

    <!-- HEADER PROFIL TOKO -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8 relative">
        <!-- Banner/Cover (Placeholder pattern) -->
        <div class="h-32 md:h-48 bg-gradient-to-r from-gray-800 to-gray-900 w-full relative">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        </div>

        <div class="px-6 md:px-10 pb-6 relative">
            <div class="flex flex-col md:flex-row items-end -mt-12 md:-mt-16 gap-6">
                <!-- Foto Profil -->
                <div class="relative">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white shadow-md overflow-hidden bg-white">
                        <img src="{{ $seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($seller->name).'&background=random' }}" 
                             class="w-full h-full object-cover" alt="{{ $seller->name }}">
                    </div>
                </div>

                <!-- Info Toko -->
                <div class="flex-grow pb-2 text-center md:text-left">
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2 justify-center md:justify-start">
                        {{ $seller->name }}
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full border border-blue-200">Official Seller</span>
                    </h1>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mt-2 justify-center md:justify-start">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $seller->addresses->first()->city_name ?? 'Indonesia' }}
                        </span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Bergabung {{ $seller->created_at->format('M Y') }}
                        </span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                         <span class="flex items-center gap-1 font-medium text-gray-900">
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ $rating }} / 5.0
                        </span>
                    </div>
                </div>

                <!-- Stats Box -->
                <div class="flex gap-8 border-t md:border-t-0 md:border-l border-gray-200 pt-4 md:pt-0 pl-0 md:pl-8 mb-2 justify-center md:justify-start">
                    <div class="text-center md:text-left">
                        <span class="block text-xl font-bold text-gray-900">{{ $products->total() }}</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Produk</span>
                    </div>
                    <div class="text-center md:text-left">
                        <span class="block text-xl font-bold text-gray-900">{{ $totalSales }}</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Terjual</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LIST PRODUK -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900">Etalase Toko</h2>
        
        <!-- Search within shop (Optional UI) -->
        <div class="relative w-full max-w-xs hidden sm:block">
            <input type="text" placeholder="Cari di toko ini..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#EC1C25] focus:border-[#EC1C25]">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-12 text-center">
             <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4 mx-auto text-gray-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Toko Belum Memiliki Produk</h3>
            <p class="text-gray-500">Kunjungi lagi nanti untuk melihat produk terbaru.</p>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            @foreach($products as $product)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group overflow-hidden relative">
                    
                    <a href="{{ route('product.show', $product->product_id) }}" class="absolute inset-0 z-10" title="{{ $product->title }}"></a>

                    <div class="relative block h-40 overflow-hidden bg-gray-100">
                        <img class="object-cover w-full h-full group-hover:scale-105 transition duration-500"
                             src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/400x400?text=No+Image' }}"
                             alt="{{ $product->title }}">
                        
                        @if($product->condition == 'new')
                            <span class="absolute top-2 left-2 bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm z-20">BARU</span>
                        @endif
                    </div>

                    <div class="p-3 flex flex-col flex-grow relative z-20 pointer-events-none">
                        <h5 class="text-sm font-medium text-gray-900 line-clamp-2 leading-snug min-h-[40px] group-hover:text-[#EC1C25] transition-colors mb-2">
                            {{ $product->title }}
                        </h5>

                        <p class="text-base font-bold text-gray-900">
                            Rp{{ number_format($product->price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{ $products->links('pagination::tailwind') }}
        </div>
    @endif

</div>
@endsection
