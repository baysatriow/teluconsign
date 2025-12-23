@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F8F9FA]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- SIDEBAR FILTERS -->
            <div class="w-full lg:w-72 flex-shrink-0 space-y-6">
                <!-- Mobile Filter Toggle -->
                <div class="lg:hidden mb-4">
                    <button id="mobileFilterBtn" class="w-full bg-white border border-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl flex items-center justify-between shadow-sm">
                        <span>Filter Produk</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </button>
                </div>

                <div id="filterSidebar" class="hidden lg:block bg-white p-6 rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/40 sticky top-24 transition-all">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                             <svg class="w-5 h-5 text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            Filter
                        </h3>
                        <a href="{{ route('search.index') }}" class="text-xs text-gray-400 font-medium hover:text-[#EC1C25] transition-colors">Reset All</a>
                    </div>

                    <form action="{{ route('search.index') }}" method="GET" id="filterForm">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                        <!-- Kategori -->
                        <div class="mb-8">
                            <h4 class="text-sm font-extrabold text-gray-900 mb-4 uppercase tracking-wide">Kategori</h4>
                            <div class="space-y-1 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="category" value="" class="text-[#EC1C25] focus:ring-[#EC1C25] border-gray-300 w-4 h-4" {{ !request('category') ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 font-medium">Semua Kategori</span>
                                </label>
                                @foreach($categories as $cat)
                                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors group">
                                        <input type="radio" name="category" value="{{ $cat->slug }}" class="text-[#EC1C25] focus:ring-[#EC1C25] border-gray-300 w-4 h-4" {{ request('category') == $cat->slug ? 'checked' : '' }}>
                                        <span class="text-sm text-gray-600 group-hover:text-gray-900 font-medium">{{ $cat->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Subcategory -->
                        @if(isset($selectedCategory) && $selectedCategory->children->count() > 0)
                        <div class="mb-8 pt-6 border-t border-gray-100 animate-fade-in-down">
                            <h4 class="text-sm font-extrabold text-gray-900 mb-4 uppercase tracking-wide">{{ $selectedCategory->name }}</h4>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($selectedCategory->children as $child)
                                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer group">
                                        <input type="checkbox" name="subcategory[]" value="{{ $child->category_id }}" class="rounded text-[#EC1C25] focus:ring-[#EC1C25] border-gray-300 w-4 h-4" {{ in_array($child->category_id, (array)request('subcategory', [])) ? 'checked' : '' }}>
                                        <span class="text-sm text-gray-600 group-hover:text-gray-900 font-medium">{{ $child->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Kondisi -->
                        <div class="mb-8 pt-6 border-t border-gray-100">
                            <h4 class="text-sm font-extrabold text-gray-900 mb-4 uppercase tracking-wide">Kondisi Barang</h4>
                            <div class="flex flex-col gap-2">
                                 <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer group">
                                    <input type="checkbox" name="condition" value="new" class="rounded text-[#EC1C25] focus:ring-[#EC1C25] border-gray-300 w-4 h-4" {{ request('condition') == 'new' ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 font-medium">Baru (New)</span>
                                </label>
                                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer group">
                                    <input type="checkbox" name="condition" value="used" class="rounded text-[#EC1C25] focus:ring-[#EC1C25] border-gray-300 w-4 h-4" {{ request('condition') == 'used' ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 font-medium">Bekas (Pre-loved)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Harga -->
                        <div class="mb-8 pt-6 border-t border-gray-100">
                             <h4 class="text-sm font-extrabold text-gray-900 mb-4 uppercase tracking-wide">Rentang Harga</h4>
                            <div class="space-y-3">
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm">Rp</span>
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Minimum" class="w-full pl-10 pr-4 py-2 text-sm border-gray-200 rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25]">
                                </div>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm">Rp</span>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Maksimum" class="w-full pl-10 pr-4 py-2 text-sm border-gray-200 rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25]">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-[#EC1C25] text-white font-bold py-3.5 rounded-xl shadow-lg shadow-red-500/20 hover:bg-[#c4161e] hover:shadow-red-500/40 transition-all transform hover:-translate-y-0.5 active:scale-95">
                            Terapkan Filter
                        </button>
                    </form>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="flex-grow">
                
                <!-- Breadcrumb & Header -->
                @if(isset($selectedCategory))
                <nav class="flex mb-4 text-xs font-medium text-gray-500" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-2">
                        <li><a href="{{ route('home') }}" class="hover:text-[#EC1C25]">Home</a></li>
                        <li><span class="text-gray-300">/</span></li>
                        <li><a href="{{ route('search.index') }}" class="hover:text-[#EC1C25]">Pencarian</a></li>
                         <li><span class="text-gray-300">/</span></li>
                        <li class="text-gray-900 font-bold" aria-current="page">{{ $selectedCategory->name }}</li>
                    </ol>
                </nav>
                @endif

                <!-- Search Result Header -->
                <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-5 rounded-2xl border border-gray-100 shadow-sm mb-6">
                    <div>
                        @if(request('search'))
                            <h1 class="text-lg text-gray-600">Hasil pencarian: "<span class="font-extrabold text-gray-900">{{ request('search') }}</span>"</h1>
                        @elseif(isset($selectedCategory))
                             <h1 class="text-2xl font-extrabold text-gray-900">{{ $selectedCategory->name }}</h1>
                        @else
                            <h1 class="text-xl font-extrabold text-gray-900">Semua Produk</h1>
                        @endif
                        <p class="text-xs text-gray-500 mt-1 font-medium">{{ $products->total() }} produk ditemukan</p>
                    </div>
                    
                    <div class="mt-4 sm:mt-0 flex items-center gap-3">
                        <label class="text-xs text-gray-500 font-medium hidden sm:inline">Urutkan:</label>
                        <select name="sort" form="filterForm" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm font-bold rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25] block py-2.5 px-4 cursor-pointer hover:bg-gray-100 transition-colors">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        </select>
                    </div>
                </div>

                <!-- Product Grid -->
                @if($products->isEmpty())
                    <div class="flex flex-col items-center justify-center py-24 text-center bg-white rounded-3xl border border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6 text-gray-300 animate-pulse">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Produk Tidak Ditemukan</h3>
                        <p class="text-gray-500 mb-8 max-w-sm mx-auto">Kami tidak dapat menemukan produk yang sesuai dengan filter Anda. Coba kata kunci lain atau reset filter.</p>
                        <a href="{{ route('search.index') }}" class="px-8 py-3 bg-gray-900 text-white rounded-xl font-bold hover:bg-gray-800 transition-shadow shadow-lg hover:shadow-xl">
                            Reset Filter
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                        @foreach($products as $product)
                            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group overflow-hidden relative">
                                
                                <a href="{{ route('product.show', $product->slug ?? $product->product_id) }}" class="absolute inset-0 z-10"></a>

                                <!-- Image -->
                                <div class="relative bg-gray-100 overflow-hidden pt-[100%]">
                                    <img class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-in-out"
                                         src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/400x400?text=No+Image' }}"
                                         alt="{{ $product->title }}">
                                    
                                     <div class="absolute top-3 left-3 z-20 flex flex-col gap-2">
                                        @if($product->condition == 'new')
                                            <span class="bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm">BARU</span>
                                        @else
                                            <span class="bg-gray-800 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm">BEKAS</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-4 flex flex-col flex-grow">
                                     <div class="flex justify-between items-start mb-2">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider line-clamp-1">{{ $product->category->name }}</span>
                                        <div class="flex items-center gap-1 text-yellow-500 text-xs font-bold bg-yellow-50 px-1.5 py-0.5 rounded-md">
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                            <span class="text-gray-800">{{ $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : '0' }}</span>
                                        </div>
                                     </div>

                                    <h5 class="text-sm font-bold text-gray-900 line-clamp-2 leading-relaxed mb-1 group-hover:text-[#EC1C25] transition-colors min-h-[40px]">
                                        {{ $product->title }}
                                    </h5>

                                    <div class="mt-auto">
                                        <p class="text-lg font-extrabold text-[#EC1C25] mb-3">
                                            Rp{{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                        
                                        <div class="flex items-center justify-between pt-3 border-t border-gray-50 text-xs text-gray-500">
                                            <div class="flex items-center gap-1.5 truncate max-w-[70%]">
                                                <div class="w-5 h-5 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
                                                     <img src="{{ $product->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->seller->name) }}" class="w-full h-full object-cover">
                                                </div>
                                                <span class="truncate font-medium">{{ $product->seller->name }}</span>
                                            </div>
                                            <span class="font-medium text-gray-400">{{ $product->location ?? $product->seller->addresses->first()->city ?? 'Bandung' }}</span>
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

        </div>
    </div>
</div>

<script>
    // Simple script for mobile filter toggle
    document.getElementById('mobileFilterBtn').addEventListener('click', function() {
        const sidebar = document.getElementById('filterSidebar');
        sidebar.classList.toggle('hidden');
    });
</script>
@endsection
