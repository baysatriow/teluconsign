@extends('layouts.seller')

@section('content')
<div class="space-y-6">

    <!-- Header & Filters -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h2>
            <p class="text-sm text-gray-500">Ringkasan performa penjualan anda.</p>
        </div>
        
        <form method="GET" action="{{ route('shop.reports') }}" class="flex items-center gap-2 bg-white p-1 rounded-xl shadow-sm border border-gray-100">
            <select name="month" class="bg-gray-50 border-0 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block py-2.5 px-4 font-medium cursor-pointer hover:bg-gray-100 transition-colors">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>
            <select name="year" class="bg-gray-50 border-0 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block py-2.5 px-4 font-medium cursor-pointer hover:bg-gray-100 transition-colors">
                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-telu-red text-white p-2.5 rounded-lg hover:bg-red-700 transition-colors shadow-md shadow-red-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card-premium p-6 flex items-center justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-green-50 rounded-full translate-x-10 -translate-y-10 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium">Total Pendapatan ({{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }})</p>
                <div class="flex items-baseline gap-1 mt-2">
                    <span class="text-xs text-gray-400 font-medium">Rp</span>
                    <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="relative z-10 p-3 bg-green-100 rounded-xl text-green-600 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="card-premium p-6 flex items-center justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 rounded-full translate-x-10 -translate-y-10 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium">Total Transaksi</p>
                <h3 class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $totalOrders }} <span class="text-sm font-medium text-gray-400">Pesanan</span></h3>
            </div>
            <div class="relative z-10 p-3 bg-blue-100 rounded-xl text-blue-600 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Daily Breakdown Table -->
    <div class="card-premium overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800">Rincian Penjualan Harian</h3>
        </div>
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Tanggal</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Jumlah Transaksi</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($dailySales as $sale)
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">
                             {{ \Carbon\Carbon::parse($sale->date)->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-50 text-blue-700 py-1 px-3 rounded-md font-bold text-xs">{{ $sale->count }}</span>
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900">
                            Rp{{ number_format($sale->revenue, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data penjualan untuk periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($dailySales) > 0)
                <tfoot class="bg-gray-50 font-bold text-gray-900">
                    <tr>
                        <td class="px-6 py-4">Total</td>
                        <td class="px-6 py-4 text-center">{{ $totalOrders }}</td>
                        <td class="px-6 py-4 text-right">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection
