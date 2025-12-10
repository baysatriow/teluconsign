@extends('layouts.settings')

@section('settings_content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Kategori Produk</h1>
        <p class="text-gray-500 text-sm">Kelola kategori untuk mengelompokkan produk Anda.</p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r shadow-sm animate-fade-in-down">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <div class="ml-3"><p class="text-sm text-green-700 font-medium">{{ session('success') }}</p></div>
    </div>
</div>
@endif

@if(session('error'))
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r shadow-sm animate-fade-in-down">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        </div>
        <div class="ml-3"><p class="text-sm text-red-700 font-medium">{{ session('error') }}</p></div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Tambah Baru</h3>
            </div>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Kategori</label>
                    <input type="text" name="name"
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5 @error('name') border-red-500 @enderror"
                           placeholder="Contoh: Elektronik, Pakaian"
                           value="{{ old('name') }}"
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-[var(--tc-btn-bg)] hover:bg-gray-800 text-white font-bold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center shadow-md group">
                    <span>Simpan Kategori</span>
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700">Daftar Kategori</h3>
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded-full">{{ $categories->total() }} Total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $category)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-0.5 rounded inline-block">{{ $category->slug }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('categories.destroy', $category->category_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori &quot;{{ $category->name }}&quot;? \nPastikan tidak ada produk di kategori ini.')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-2 rounded-full hover:bg-red-50 transition-all" title="Hapus Kategori">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 p-3 rounded-full mb-3">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">Belum ada kategori.</p>
                                    <p class="text-gray-400 text-sm">Silakan tambahkan kategori melalui form di sebelah kiri.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Animasi sederhana untuk alert */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translate3d(0, -10px, 0); }
        to { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    .animate-fade-in-down {
        animation: fadeInDown 0.3s ease-out;
    }
</style>
@endsection
