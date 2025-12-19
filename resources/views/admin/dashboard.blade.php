@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
    <p class="text-sm text-gray-500">Ringkasan aktivitas platform Telu Consign hari ini.</p>
</div>

<!-- Kartu Statistik Utama -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Total Users -->
    <div class="p-5 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pengguna</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_users'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
        <div class="flex items-center text-sm text-green-600">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            <span class="font-medium">Total Akun Terdaftar</span>
        </div>
    </div>

    <!-- Total Produk -->
    <div class="p-5 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Produk Aktif</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_products'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
        </div>
        <div class="flex items-center text-sm text-gray-500">
            <span class="font-medium">Total Item di Marketplace</span>
        </div>
    </div>

    <!-- Payout Requests -->
    <div class="p-5 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        @if($stats['pending_payouts'] > 0)
            <div class="absolute top-0 right-0 w-2 h-full bg-orange-500"></div>
        @endif
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Payout Pending</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending_payouts'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="flex items-center text-sm {{ $stats['pending_payouts'] > 0 ? 'text-orange-600 font-bold' : 'text-green-600' }}">
            {{ $stats['pending_payouts'] > 0 ? 'Perlu Diproses Segera' : 'Semua Payout Lunas' }}
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="p-5 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pendapatan Platform</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp{{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="flex items-center text-sm text-gray-500">
            <span class="font-medium">Total Fee Transaksi</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Chart Section -->
    <div class="lg:col-span-2 p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Tren Transaksi Selesai</h3>
            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-0.5 rounded">7 Hari Terakhir</span>
        </div>
        <div class="relative h-72">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Pengguna Terbaru</h3>
        <ul class="divide-y divide-gray-100">
            @forelse($recentUsers as $user)
            <li class="py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 text-xs font-bold uppercase">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ Str::limit($user->name, 15) }}</p>
                        <p class="text-xs text-gray-500">{{ Str::limit($user->email, 20) }}</p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $user->role === 'seller' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </li>
            @empty
            <li class="text-center text-gray-500 text-sm py-4">Belum ada pengguna.</li>
            @endforelse
        </ul>
        <a href="{{ route('admin.users') }}" class="block text-center text-sm text-[#EC1C25] font-medium mt-4 hover:underline">Lihat Semua Pengguna</a>
    </div>
</div>

<!-- Recent Products Table -->
<div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-900">Postingan Produk Terbaru</h3>
        <a href="{{ route('admin.products') }}" class="text-sm text-[#EC1C25] font-medium hover:underline">Kelola Semua Produk</a>
    </div>

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Produk</th>
                    <th scope="col" class="px-6 py-3">Penjual</th>
                    <th scope="col" class="px-6 py-3">Harga</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentProducts as $product)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap flex items-center gap-3">
                        <img class="w-10 h-10 rounded object-cover border border-gray-200" src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/100' }}" alt="">
                        {{ Str::limit($product->title, 30) }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $product->seller->name }}
                    </td>
                    <td class="px-6 py-4">
                        Rp{{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($product->status->value === 'active')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Aktif</span>
                        @elseif($product->status->value === 'suspended')
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Suspend</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ ucfirst($product->status->value) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ $product->created_at->format('d M Y') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-6 text-gray-500">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Inisialisasi Chart.js
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');

        // Data dari Controller
        const labels = @json($chartLabels);
        const data = @json($chartValues);

        // Styling Gradient
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(236, 28, 37, 0.2)'); // Merah Telkom Transparan
        gradient.addColorStop(1, 'rgba(236, 28, 37, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Transaksi Selesai',
                    data: data,
                    borderColor: '#EC1C25',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#EC1C25',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4 // Garis melengkung halus
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#f3f4f6' },
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endsection
