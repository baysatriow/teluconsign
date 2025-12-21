@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Dashboard</h1>
    <p class="text-sm text-gray-500 mt-1">Hello {{ Auth::user()->name }}, berikut ringkasan aktivitas hari ini.</p>
</div>

<!-- Kartu Statistik Utama -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <!-- Total Users -->
    <div class="p-6 bg-white rounded-2xl shadow-soft border border-gray-100 transition-all hover:-translate-y-1 hover:shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pengguna</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_users']) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
        <div class="flex items-center text-xs font-medium text-green-600 bg-green-50 w-fit px-2 py-1 rounded-lg">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            <span>Terdaftar v.s Akun Aktif</span>
        </div>
    </div>

    <!-- Total Produk -->
    <div class="p-6 bg-white rounded-2xl shadow-soft border border-gray-100 transition-all hover:-translate-y-1 hover:shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Produk</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_products']) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
        </div>
        <div class="flex items-center text-xs font-medium text-gray-500 bg-gray-50 w-fit px-2 py-1 rounded-lg">
            <span>Item tersedia di katalog</span>
        </div>
    </div>

    <!-- Payout Requests -->
    <div class="p-6 bg-white rounded-2xl shadow-soft border border-gray-100 transition-all hover:-translate-y-1 hover:shadow-lg relative overflow-hidden group">
        @if($stats['pending_payouts'] > 0)
            <div class="absolute top-0 right-0 w-1.5 h-full bg-orange-500"></div>
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        @endif
        <div class="flex items-center justify-between mb-4 relative z-10">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Payout Pending</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['pending_payouts']) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="flex items-center text-xs font-bold w-fit px-2 py-1 rounded-lg {{ $stats['pending_payouts'] > 0 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700' }}">
            {{ $stats['pending_payouts'] > 0 ? 'Perlu Diproses' : 'Semua Beres' }}
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="p-6 bg-white rounded-2xl shadow-soft border border-gray-100 transition-all hover:-translate-y-1 hover:shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pendapatan</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp{{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="flex items-center text-xs font-medium text-gray-500 bg-gray-50 w-fit px-2 py-1 rounded-lg">
            <span>Total Fee Transaksi</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Chart Section -->
    <div class="lg:col-span-2 p-6 bg-white rounded-2xl shadow-soft border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Analitik Penjualan</h3>
            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">7 Hari Terakhir</span>
        </div>
        <div class="relative h-80">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="p-6 bg-white rounded-2xl shadow-soft border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Pengguna Baru</h3>
            <a href="{{ route('admin.users') }}" class="text-xs font-bold text-telu-red hover:underline">Lihat Semua</a>
        </div>
        <ul class="space-y-4">
            @forelse($recentUsers as $user)
            <li class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <img class="w-10 h-10 rounded-full border border-gray-200 object-cover" src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=Random&color=fff' }}" alt="">
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ Str::limit($user->name, 15) }}</p>
                        <p class="text-xs text-gray-500">{{ Str::limit($user->email, 20) }}</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold px-2 py-1 rounded-lg uppercase tracking-wider {{ $user->role === 'seller' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $user->role }}
                </span>
            </li>
            @empty
            <li class="text-center text-gray-500 text-sm py-8 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                Belum ada pengguna baru.
            </li>
            @endforelse
        </ul>
    </div>
</div>

<!-- Recent Products Table -->
<div class="p-8 bg-white rounded-2xl shadow-soft border border-gray-100">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Produk Masuk</h3>
            <p class="text-sm text-gray-500">Daftar produk terbaru yang diunggah pengguna.</p>
        </div>
        <a href="{{ route('admin.products') }}" class="text-sm text-white bg-gray-900 hover:bg-black px-4 py-2 rounded-xl font-medium shadow-lg transition-transform hover:-translate-y-0.5">
            Kelola Produk
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50/50">
                <tr>
                    <th scope="col" class="px-6 py-4">Produk</th>
                    <th scope="col" class="px-6 py-4">Penjual</th>
                    <th scope="col" class="px-6 py-4">Harga</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentProducts as $product)
                <tr class="bg-white hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap flex items-center gap-4">
                        <img class="w-12 h-12 rounded-lg object-cover border border-gray-200 shadow-sm" src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/100' }}" alt="">
                        <div>
                            <div class="font-bold text-gray-900">{{ Str::limit($product->title, 30) }}</div>
                            <div class="text-xs text-gray-500">ID: #{{ $product->product_id }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px] font-bold">
                                {{ substr($product->seller->name, 0, 1) }}
                            </div>
                            <span class="font-medium text-gray-700">{{ $product->seller->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-900">
                        Rp{{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($product->status->value === 'active')
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1 w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                            </span>
                        @elseif($product->status->value === 'suspended')
                            <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1 w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Suspended
                            </span>
                        @else
                            <span class="bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1 rounded-full w-fit">
                                {{ ucfirst($product->status->value) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-500 font-mono text-xs">
                        {{ $product->created_at->format('d M Y') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-12 text-gray-400 font-medium">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = @json($chartLabels);
        const data = @json($chartValues);

        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(236, 28, 37, 0.15)'); 
        gradient.addColorStop(1, 'rgba(236, 28, 37, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Transaksi',
                    data: data,
                    borderColor: '#EC1C25',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#EC1C25',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.4
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
                        backgroundColor: '#1f2937',
                        titleFont: { size: 14, family: "'Plus Jakarta Sans', sans-serif" },
                        bodyFont: { size: 14, family: "'Plus Jakarta Sans', sans-serif" },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Plus Jakarta Sans', sans-serif" }, color: '#9ca3af' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#f3f4f6' },
                        ticks: { font: { family: "'Plus Jakarta Sans', sans-serif" }, color: '#9ca3af', stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endsection
