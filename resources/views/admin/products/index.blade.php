@extends('layouts.admin')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Manajemen Postingan Produk</h1>
            <p class="text-sm text-gray-500">Review, pantau, dan kelola semua produk yang dijual di platform.</p>
        </div>

        <!-- Statistik Mini -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                <div class="text-xs text-blue-600 font-medium uppercase">Total Produk</div>
                <div class="text-xl font-bold text-gray-800">{{ $stats['total'] }}</div>
            </div>
            <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                <div class="text-xs text-green-600 font-medium uppercase">Aktif Tayang</div>
                <div class="text-xl font-bold text-gray-800">{{ $stats['active'] }}</div>
            </div>
            <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                <div class="text-xs text-red-600 font-medium uppercase">Disuspend</div>
                <div class="text-xl font-bold text-gray-800">{{ $stats['suspended'] }}</div>
            </div>
             <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                <div class="text-xs text-gray-500 font-medium uppercase">Perlu Review</div>
                <div class="text-xl font-bold text-gray-800">0</div> <!-- Placeholder -->
            </div>
        </div>

        <div class="sm:flex">
            <!-- Search & Filter Form -->
            <form action="{{ route('admin.products') }}" method="GET" class="flex flex-col sm:flex-row items-center w-full sm:divide-x sm:divide-gray-100 sm:mb-0 gap-4">
                <div class="relative w-full sm:w-64 xl:w-96">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5" placeholder="Cari produk atau penjual...">
                </div>

                <div class="flex items-center w-full sm:justify-end gap-2 sm:pl-4">
                    <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full sm:w-auto p-2.5">
                        <option value="">Semua Status</option>
                        @foreach(App\Enums\ProductStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>

                    <select name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full sm:w-auto p-2.5">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ request('category') == $cat->category_id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Filter
                    </button>

                    @if(request()->has('q') || request()->has('status') || request()->has('category'))
                        <a href="{{ route('admin.products') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<div class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                Produk
                            </th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                Kategori
                            </th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                Penjual
                            </th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                Harga
                            </th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                Status
                            </th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                <div class="flex items-center gap-3">
                                    <img class="w-10 h-10 rounded object-cover border border-gray-200" src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/100' }}" alt="">
                                    <div class="text-base font-semibold text-gray-900 dark:text-white">{{ Str::limit($product->title, 40) }}</div>
                                </div>
                                <div class="text-xs text-gray-400 mt-1">ID: #{{ $product->product_id }} • {{ $product->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->category->name ?? '-' }}
                            </td>
                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-bold">
                                        {{ substr($product->seller->name, 0, 1) }}
                                    </div>
                                    {{ $product->seller->name }}
                                </div>
                            </td>
                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-base font-normal text-gray-900 whitespace-nowrap dark:text-white">
                                <span class="bg-{{ $product->status->color() }}-100 text-{{ $product->status->color() }}-800 text-xs font-medium px-2.5 py-0.5 rounded border border-{{ $product->status->color() }}-200">
                                    {{ $product->status->label() }}
                                </span>
                            </td>
                            <td class="p-4 space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.products.show', $product->product_id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-indigo-700 hover:bg-indigo-800 focus:ring-4 focus:ring-indigo-300 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
                                    Review
                                </a>

                                <form action="{{ route('admin.products.toggle_status', $product->product_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status produk ini?');">
                                    @csrf
                                    @method('PATCH')
                                    @if($product->status === App\Enums\ProductStatus::Suspended)
                                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Aktifkan
                                        </button>
                                    @else
                                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            Suspend
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500 py-8">
                                Tidak ada produk yang ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        {{ $products->links('pagination::tailwind') }}
    </div>
</div>
@endsection
