@extends('layouts.admin')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 mb-6">
    <div class="w-full flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Review Produk #{{ $product->product_id }}</h1>
        <a href="{{ route('admin.products') }}" class="text-gray-500 hover:text-gray-900 text-sm font-medium">
            &larr; Kembali ke Daftar
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Kolom Kiri: Gambar & Info Utama -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $product->title }}</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                @foreach($product->images as $img)
                    <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-100">
                        <img src="{{ asset('storage/'.$img->url) }}" class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>

            <div class="prose max-w-none text-gray-600">
                <h4 class="font-semibold text-gray-900">Deskripsi:</h4>
                <p class="whitespace-pre-line">{{ $product->description }}</p>
            </div>
        </div>

        <!-- Tabel Detail -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Atribut</th>
                        <th class="px-6 py-3">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <th class="px-6 py-4 font-medium text-gray-900">Harga</th>
                        <td class="px-6 py-4">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-b">
                        <th class="px-6 py-4 font-medium text-gray-900">Stok</th>
                        <td class="px-6 py-4">{{ $product->stock }}</td>
                    </tr>
                    <tr class="border-b">
                        <th class="px-6 py-4 font-medium text-gray-900">Kondisi</th>
                        <td class="px-6 py-4 capitalize">{{ $product->condition }}</td>
                    </tr>
                    <tr class="border-b">
                        <th class="px-6 py-4 font-medium text-gray-900">Kategori</th>
                        <td class="px-6 py-4">{{ $product->category->name }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-4 font-medium text-gray-900">Lokasi</th>
                        <td class="px-6 py-4">{{ $product->location }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kolom Kanan: Info Seller & Aksi Admin -->
    <div class="space-y-6">

        <!-- Status Panel -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-base font-bold text-gray-900 mb-4 uppercase tracking-wide text-xs">Status Moderasi</h3>

            <div class="flex items-center justify-between mb-6">
                <span class="text-sm text-gray-500">Status Saat Ini:</span>
                <span class="bg-{{ $product->status->color() }}-100 text-{{ $product->status->color() }}-800 text-sm font-bold px-3 py-1 rounded-full border border-{{ $product->status->color() }}-200">
                    {{ $product->status->label() }}
                </span>
            </div>

            <hr class="border-gray-100 mb-6">

            <h4 class="text-sm font-semibold text-gray-900 mb-2">Tindakan Admin:</h4>

            <form action="{{ route('admin.products.toggle_status', $product->product_id) }}" method="POST">
                @csrf
                @method('PATCH')

                @if($product->status === App\Enums\ProductStatus::Suspended)
                    <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
                        <span class="font-medium">Info:</span> Produk ini sedang disuspend. Aktifkan kembali jika pelanggaran sudah diperbaiki.
                    </div>
                    <button type="submit" class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 focus:outline-none">
                        Aktifkan Kembali (Restore)
                    </button>
                @else
                    <div class="mb-4">
                        <label for="reason" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alasan Suspend (Opsional)</label>
                        <textarea id="reason" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Barang ilegal / Deskripsi kasar..."></textarea>
                    </div>
                    <button type="submit" class="w-full text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 focus:outline-none">
                        Suspend Produk Ini
                    </button>
                @endif
            </form>
        </div>

        <!-- Info Penjual -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="text-base font-bold text-gray-900 mb-4 uppercase tracking-wide text-xs">Informasi Penjual</h3>
            <div class="flex items-center gap-4 mb-4">
                <img class="w-12 h-12 rounded-full border border-gray-200" src="{{ $product->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->seller->name) }}" alt="">
                <div>
                    <div class="font-bold text-gray-900">{{ $product->seller->name }}</div>
                    <div class="text-xs text-gray-500">{{ $product->seller->email }}</div>
                </div>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Bergabung:</span>
                    <span class="font-medium">{{ $product->seller->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Status Akun:</span>
                    <span class="font-medium {{ $product->seller->status === 'active' ? 'text-green-600' : 'text-red-600' }}">{{ ucfirst($product->seller->status) }}</span>
                </div>
            </div>
            <a href="#" class="block text-center text-blue-600 hover:underline text-sm mt-4 font-medium">Lihat Detail User</a>
        </div>

    </div>
</div>
@endsection
