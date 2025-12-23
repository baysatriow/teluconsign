@extends('layouts.seller')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Pesanan Masuk</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola dan pantau status pesanan dari pelanggan anda.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- TOTAL ORDERS -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Pesanan</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <!-- SIAP DIKIRIM (Paid + Processed) -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-yellow-50 text-yellow-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Perlu Proses</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['paid'] + $stats['processed'] }}</p>
            </div>
        </div>
        <!-- SELESAI -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-green-50 text-green-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Selesai</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
            </div>
        </div>
        <!-- PEMBATALAN/REFUND -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-red-50 text-red-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Batal / Refund</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['cancelled'] + $stats['refunded'] }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        
        <!-- Toolbar -->
        <div class="border-b border-gray-100 bg-gray-50/50 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            
            <!-- Tabs -->
            <nav class="flex space-x-1 bg-gray-100/50 p-1 rounded-xl overflow-x-auto min-w-0 max-w-full" aria-label="Tabs">
                @php
                    $tabs = [
                        'all'       => ['label' => 'Semua', 'count' => $stats['total']],
                        'pending'   => ['label' => 'Belum Bayar', 'count' => $stats['pending']],
                        'paid'      => ['label' => 'Dibayar', 'count' => $stats['paid']],
                        'processed' => ['label' => 'Diproses', 'count' => $stats['processed']],
                        'shipped'   => ['label' => 'Dikirim', 'count' => $stats['shipped']],
                        'completed' => ['label' => 'Selesai', 'count' => $stats['completed']],
                        'cancelled' => ['label' => 'Dibatalkan', 'count' => $stats['cancelled']],
                        'refunded'  => ['label' => 'Pengembalian', 'count' => $stats['refunded']],
                    ];
                @endphp

                @foreach($tabs as $key => $data)
                <a href="{{ route('shop.orders', ['tab' => $key, 'q' => request('q')]) }}" 
                   class="whitespace-nowrap flex items-center gap-2 py-2 px-3 rounded-lg text-sm font-medium transition-all {{ $tab == $key ? 'bg-white text-gray-900 shadow-sm ring-1 ring-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50' }}">
                   {{ $data['label'] }}
                   @if($key !== 'all' && $data['count'] > 0)
                   <span class="{{ $tab == $key ? 'bg-gray-100 text-gray-900' : 'bg-gray-200 text-gray-600' }} py-0.5 px-2 rounded-md text-[10px] font-bold transition-colors">
                       {{ $data['count'] }}
                   </span>
                   @endif
                </a>
                @endforeach
            </nav>

            <!-- Search -->
            <form action="{{ route('shop.orders') }}" method="GET" class="w-full md:w-64 relative">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="search" name="q" value="{{ request('q') }}" 
                           class="block w-full p-2.5 pl-10 pr-20 text-sm text-gray-900 border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-0 focus:border-telu-red transition-colors" 
                           placeholder="Cari No. Pesanan / Pembeli...">
                    <button type="submit" class="absolute right-1 top-1 bottom-1 px-4 bg-telu-red text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-colors">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Order List -->
        <div class="relative overflow-x-auto min-h-[400px]">
            <table class="w-full text-sm text-left align-middle">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Info Pesanan</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Total Belanja</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="font-mono text-xs text-gray-500">{{ $order->code }}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 overflow-hidden">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($order->buyer->name) }}&background=random" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $order->buyer->name }}</span>
                                </div>
                                <span class="text-xs text-gray-400 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                            <div class="text-xs text-gray-400 font-normal mt-0.5">{{ $order->items->count() }} Produk</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending'   => 'yellow',
                                    'paid'      => 'blue',
                                    'processed' => 'indigo',
                                    'shipped'   => 'purple',
                                    'completed' => 'green',
                                    'cancelled' => 'red',
                                    'refunded'  => 'pink',
                                ];
                                $color = $statusColors[$order->status] ?? 'gray';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 bg-{{ $color }}-50 text-{{ $color }}-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-{{ $color }}-200 uppercase tracking-wide">
                                <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500"></span>
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('shop.orders.show', $order) }}" 
                               class="inline-block px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium border border-blue-200 hover:bg-blue-100 transition-colors">
                                Detail
                            </a>
                        </td>
                    </tr>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada pesanan</h3>
                                <p class="text-gray-400 text-sm max-w-xs mx-auto">Pesanan yang masuk akan tampil di sini. Promosikan toko anda untuk mendapatkan pesanan!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="p-4 border-t border-gray-100 flex justify-center bg-gray-50">
            {{ $orders->links('pagination::tailwind') }}
        </div>
        @endif
        
    </div>
</div>

@endsection
