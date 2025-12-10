@extends('layouts.settings')

@section('settings_content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Edit Produk</h1>
        <p class="text-gray-500 text-sm">Perbarui informasi produk: <span class="font-semibold text-gray-700">{{ $product->title }}</span></p>
    </div>
    <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
    </a>
</div>

{{-- Error Display Global (jika ada yang lolos dari JS) --}}
@if($errors->any())
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
    <div class="flex">
        <div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg></div>
        <div class="ml-3">
            <p class="text-sm text-red-700 font-bold">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

{{-- Panggil Komponen Form --}}
@include('components.product-form', [
    'action' => route('products.update', $product->product_id),
    'method' => 'PUT',
    'product' => $product,
    'categories' => $categories,
    'conditions' => $conditions
])

@endsection
