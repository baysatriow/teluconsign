@extends('layouts.settings')

@section('settings_content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Detail Produk</h1>
        <p class="text-gray-500 text-sm">ID: #{{ $product->product_id }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('products.index') }}" class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-50 text-sm font-medium">Kembali</a>
        <a href="{{ route('products.edit', $product->product_id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium shadow">Edit Produk</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-0">

        {{-- KOLOM KIRI: GAMBAR --}}
        <div class="p-6 bg-gray-50 border-r border-gray-100">
            {{-- Gambar Utama Besar --}}
            <div class="mb-4 rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-white aspect-square flex items-center justify-center">
                @if($product->main_image)
                    <img id="mainPreview" src="{{ asset('storage/' . $product->main_image) }}" class="w-full h-full object-contain">
                @else
                    <span class="text-gray-400">No Image</span>
                @endif
            </div>

            {{-- Thumbnail List --}}
            <div class="flex gap-2 overflow-x-auto pb-2">
                @foreach($product->images as $img)
                    <button onclick="document.getElementById('mainPreview').src='{{ asset('storage/' . $img->url) }}'"
                            class="w-16 h-16 rounded-md border border-gray-300 overflow-hidden flex-shrink-0 hover:ring-2 ring-indigo-500 transition-all focus:outline-none">
                        <img src="{{ asset('storage/' . $img->url) }}" class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
        </div>

        {{-- KOLOM KANAN: INFO --}}
        <div class="p-8">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wide bg-indigo-50 px-2 py-1 rounded mb-2 inline-block">
                        {{ $product->category->name ?? 'Uncategorized' }}
                    </span>
                    <h2 class="text-3xl font-bold text-gray-900 leading-tight">{{ $product->title }}</h2>
                </div>
                <div class="text-right">
                     <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                        {{ $product->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $product->status }}
                    </span>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-4xl font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">Stok: <span class="font-bold text-gray-800">{{ $product->stock }}</span> unit</p>
            </div>

            <hr class="border-gray-100 my-6">

            <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                <div>
                    <span class="block text-gray-500 text-xs uppercase font-bold">Kondisi</span>
                    <span class="text-gray-800 font-medium capitalize">{{ $product->condition == 'new' ? 'Baru' : 'Bekas' }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 text-xs uppercase font-bold">Lokasi</span>
                    <span class="text-gray-800 font-medium">{{ $product->location }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 text-xs uppercase font-bold">Diposting</span>
                    <span class="text-gray-800 font-medium">{{ $product->created_at->diffForHumans() }}</span>
                </div>
            </div>

            <div>
                <span class="block text-gray-500 text-xs uppercase font-bold mb-2">Deskripsi</span>
                <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
            </div>

            {{-- Tombol Aksi Cepat --}}
            <div class="mt-10 pt-6 border-t border-gray-100">
                <form action="{{ route('products.changeStatus', $product->product_id) }}" method="POST">
                    @csrf
                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="hidden" name="status" value="{{ $product->status == 'active' ? 'archived' : 'active' }}">
                            <button type="submit" class="text-sm font-medium text-indigo-600 hover:underline">
                                {{ $product->status == 'active' ? 'Arsipkan Produk Ini (Sembunyikan)' : 'Aktifkan Kembali Produk' }}
                            </button>
                        </div>
                    </label>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
