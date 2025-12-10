@extends('layouts.settings')

@section('settings_content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Jual Produk Baru</h1>
    <p class="text-gray-500 text-sm">Lengkapi detail produk Anda untuk mulai berjualan.</p>
</div>

{{-- Error Handling Sama --}}
@if($errors->any())
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
    {{-- ... (sama seperti edit) ... --}}
    <ul class="list-disc list-inside text-sm text-red-600">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@include('components.product-form', [
    'action' => route('products.store'),
    'method' => 'POST',
    'product' => null,
    'categories' => $categories,
    'conditions' => $conditions ?? [] // Fallback jika controller create belum kirim conditions
])

@endsection
