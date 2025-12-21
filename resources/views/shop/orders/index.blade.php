@extends('layouts.seller')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Pesanan Masuk</h2>
            <p class="text-sm text-gray-500">Kelola pesanan yang masuk dari pembeli.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card-premium p-4">
            <p class="text-xs text-gray-500 uppercase font-bold">Total Pesanan</p>
            <p class="text-xl font-bold text-gray-900">{{ $orders->total() }}</p>
        </div>
        <div class="card-premium p-4">
            <p class="text-xs text-gray-500 uppercase font-bold">Perlu Diproses</p>
            <p class="text-xl font-bold text-blue-600">
                {{ \App\Models\Order::where('seller_id', Auth::id())->where('status', 'pending')->count() }}
            </p>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card-premium overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
             <h3 class="font-bold text-gray-800">Daftar Pesanan</h3>
        </div>
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Order ID / Tanggal</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Pembeli</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Produk</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Total</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="block font-bold text-gray-900">#{{ $order->order_id }}</span>
                            <span class="text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600">
                                    {{ substr($order->buyer->name, 0, 2) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $order->buyer->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs space-y-1">
                                @foreach($order->items as $item)
                                    <div class="text-sm text-gray-900 truncate" title="{{ $item->product_title }}">
                                        {{ $item->product_title }} <span class="text-gray-500 text-xs">x{{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900">
                            Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColor = match($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="{{ $statusColor }} text-xs font-bold px-2.5 py-0.5 rounded-md uppercase">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm hover:underline">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p>Belum ada pesanan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100 flex justify-center">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
