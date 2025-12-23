@extends('layouts.seller')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Pendapatan -->
        <div class="card-premium p-6 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-50 rounded-xl text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Total Pendapatan</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">Rp{{ number_format($stats['total_sales'], 0, ',', '.') }}</p>
        </div>

        <!-- Order Baru -->
        <a href="{{ route('shop.orders') }}" class="card-premium p-6 hover:-translate-y-1 transition-transform duration-300 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-blue-500/10 rounded-bl-full -mr-8 -mt-8 transition-all group-hover:scale-150"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                @if($stats['new'] > 0)
                    <span class="bg-red-100 text-telu-red text-xs font-bold px-3 py-1 rounded-lg animate-pulse">ACTION NEEDED</span>
                @endif
            </div>
            <h3 class="text-gray-500 text-sm font-medium group-hover:text-blue-600 transition-colors">Pesanan Baru</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1 relative z-10">{{ $stats['new'] }}</p>
        </a>

        <!-- Dikirim -->
        <div class="card-premium p-6 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-yellow-50 rounded-xl text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Sedang Dikirim</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['shipping'] }}</p>
        </div>

        <!-- Low Stock -->
        <div class="card-premium p-6 hover:-translate-y-1 transition-transform duration-300 border-l-4 {{ $stats['low_stock'] > 0 ? 'border-l-telu-red' : 'border-l-transparent' }}">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-red-50 rounded-xl text-telu-red">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Stok Menipis</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['low_stock'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Section -->
        <div class="lg:col-span-2 card-premium p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Analitik Penjualan</h3>
                <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-3 py-1 rounded-full">7 Hari Terakhir</span>
            </div>
            <div id="salesChart" class="w-full h-80"></div>
        </div>

        <!-- Recent Orders -->
        <div class="card-premium p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Pesanan Terbaru</h3>
                <a href="{{ route('shop.orders') }}" class="text-sm font-semibold text-telu-red hover:underline">Lihat Semua</a>
            </div>
            
            <div class="space-y-4">
                @forelse($recentOrders as $order)
                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors border border-dashed border-transparent hover:border-gray-200">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-600 font-bold text-xs ring-4 ring-white shadow-sm">
                            {{ substr($order->buyer->name ?? 'U', 0, 2) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $order->items->first()->product_title ?? 'Produk' }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }} &bull; Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                             @php
                                $statusColor = match($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'shipped' => 'bg-indigo-100 text-indigo-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="text-[10px] font-bold px-2 py-1 rounded-md uppercase {{ $statusColor }}">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <p class="text-sm">Belum ada pesanan masuk.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('shop.products.create') }}" class="flex items-center justify-center w-full px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-xl transition-all shadow-lg shadow-gray-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Produk Baru
                </a>
            </div>
        </div>
    </div>

</div>

<!-- Chart Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [{
                name: 'Pendapatan',
                data: @json($totals)
            }],
            chart: {
                type: 'area', 
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            colors: ['#EC1C25'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1, 
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: @json($dates),
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#94a3b8', fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontSize: '12px' },
                    formatter: function (value) {
                        if (value >= 1000000) return "Rp" + (value/1000000).toFixed(1) + "jt";
                        if (value >= 1000) return "Rp" + (value/1000).toFixed(0) + "rb";
                        return value;
                    }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (value) {
                        return "Rp" + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#salesChart"), options);
        chart.render();
    });
</script>
@endsection
