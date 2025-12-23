@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Selesaikan Pembayaran</h1>
            <p class="text-gray-600">Pilih metode pembayaran untuk melanjutkan pesanan Anda</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Order Summary - Sidebar -->
            <div class="lg:col-span-1 order-2 lg:order-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-4 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-telu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Ringkasan Pesanan
                    </h3>
                    
                    <div class="space-y-3 text-sm mb-6">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-600">Kode Pembayaran</span>
                            </div>
                            <span class="font-mono text-xs font-bold text-gray-900">{{ $payment->provider_order_id }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Jumlah Item</span>
                            <span class="font-medium text-gray-900">{{ $relatedOrders->sum(fn($o) => $o->items->count()) }} produk</span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Status Pembayaran</span>
                            @php
                                $statusBadge = match($payment->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'settlement' => 'bg-green-100 text-green-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusBadge }}">
                                {{ strtoupper($payment->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600 font-medium">Total Bayar</span>
                            <span class="text-2xl font-bold text-telu-red">Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 p-3 bg-amber-50 rounded-xl border border-amber-200">
                        <div class="flex gap-2">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs text-amber-800">
                                <strong>Penting:</strong> Selesaikan pembayaran dalam <strong class="text-amber-900">24 jam</strong> untuk menghindari pembatalan otomatis.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods - Main Content -->
            <div class="lg:col-span-2 order-1 lg:order-2">
                
                @if($payment->method_code)
                    <!-- PAYMENT ALREADY SELECTED - SHOW ONLY SELECTED METHOD -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Metode Pembayaran Dipilih</h3>
                                <p class="text-sm text-gray-500">Menunggu pembayaran Anda</p>
                            </div>
                        </div>
                        
                        <div id="payment-display">
                            <div id="payment-content">
                                <div class="text-center py-8">
                                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-telu-red mx-auto mb-4"></div>
                                    <p class="text-gray-600">Memuat detail pembayaran...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        // Auto-load payment details for selected method
                        document.addEventListener('DOMContentLoaded', function() {
                            const rawResponse = @json($payment->raw_response ?? []);
                            const methodCode = '{{ $payment->method_code }}';
                            const methodName = getMethodName(methodCode);
                            
                            if (rawResponse && Object.keys(rawResponse).length > 0) {
                                // Show existing payment data
                                showPaymentUI(methodCode, methodName, rawResponse);
                                startPaymentPolling();
                            } else {
                                // Re-fetch payment details from Midtrans
                                loadExistingPayment();
                            }
                        });

                        function loadExistingPayment() {
                            // Polling to get updated payment info
                            fetch('{{ route("payment.status", $payment) }}')
                                .then(res => res.json())
                                .then(data => {
                                    if (data.status === 'settlement') {
                                        window.location.href = '{{ route("orders.index") }}';
                                    }
                                });
                        }

                        function getMethodName(code) {
                            const map = {
                                'qris': 'QRIS',
                                'gopay': 'GoPay',
                                'shopeepay': 'ShopeePay',
                                'bca_va': 'BCA Virtual Account',
                                'bni_va': 'BNI Virtual Account',
                                'bri_va': 'BRI Virtual Account',
                                'mandiri_va': 'Mandiri Virtual Account',
                                'permata_va': 'Permata Virtual Account'
                            };
                            return map[code] || code;
                        }
                    </script>
                
                @else
                    <!-- PAYMENT METHOD SELECTION -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Pilih Metode Pembayaran</h3>
                        
                        <!-- Tab Navigation -->
                        <div class="flex gap-2 mb-6 border-b border-gray-200 overflow-x-auto pb-2">
                            <button onclick="switchTab('qr')" class="payment-tab px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors whitespace-nowrap active" data-tab="qr">
                                QR Code
                            </button>
                            <button onclick="switchTab('bank')" class="payment-tab px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors whitespace-nowrap" data-tab="bank">
                                Transfer Bank
                            </button>
                            <button onclick="switchTab('ewallet')" class="payment-tab px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors whitespace-nowrap" data-tab="ewallet">
                                E-Wallet
                            </button>
                        </div>

                        <!-- QR Code Methods -->
                        <div class="payment-tab-content" data-content="qr">
                            <div class="grid grid-cols-1 gap-3">
                                <button onclick="selectMethod('qris', 'QRIS - Semua E-Wallet')" 
                                        class="payment-method-btn group flex items-center gap-4 p-5 border-2 border-gray-200 rounded-xl hover:border-telu-red hover:bg-red-50 hover:shadow-md transition-all">
                                    <div class="w-16 h-16 bg-gradient-to-br from-pink-50 to-purple-50 rounded-xl border border-purple-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 text-left">
                                        <p class="font-bold text-gray-900 mb-1">QRIS</p>
                                        <p class="text-xs text-gray-500">Scan dengan aplikasi pembayaran apapun</p>
                                        <div class="flex gap-1 mt-2">
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded">OVO</span>
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded">GoPay</span>
                                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-medium rounded">DANA</span>
                                        </div>
                                    </div>
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-telu-red transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Virtual Account Methods -->
                        <div class="payment-tab-content hidden" data-content="bank">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($methods as $method)
                                    @if($method['category'] === 'bank_transfer')
                                    <button onclick="selectMethod('{{ $method['code'] }}', '{{ $method['name'] }}')" 
                                            class="payment-method-btn group flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl hover:border-telu-red hover:bg-red-50 hover:shadow-md transition-all">
                                        <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <span class="text-xs font-black text-blue-700">{{ strtoupper(str_replace('_va', '', $method['code'])) }}</span>
                                        </div>
                                        <div class="flex-1 text-left">
                                            <p class="font-bold text-gray-900 text-sm">{{ str_replace(' Virtual Account', '', $method['name']) }}</p>
                                            <p class="text-xs text-gray-500">Virtual Account</p>
                                        </div>
                                    </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- E-Wallet Methods -->
                        <div class="payment-tab-content hidden" data-content="ewallet">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($methods as $method)
                                    @if($method['category'] === 'ewallet')
                                    <button onclick="selectMethod('{{ $method['code'] }}', '{{ $method['name'] }}')" 
                                            class="payment-method-btn group flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl hover:border-telu-red hover:bg-red-50 hover:shadow-md transition-all">
                                        <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <span class="text-lg font-bold text-green-700">{{ substr($method['name'], 0, 2) }}</span>
                                        </div>
                                        <div class="flex-1 text-left">
                                            <p class="font-bold text-gray-900 text-sm">{{ $method['name'] }}</p>
                                            <p class="text-xs text-gray-500">E-Wallet Digital</p>
                                        </div>
                                    </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Payment Display Area (akan muncul setelah pilih metode) -->
                    <div id="payment-display" class="mt-6 bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-100 hidden">
                        <div id="payment-content"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.payment-tab.active {
    background: linear-gradient(to bottom, #EC1C25, #c4161e);
    color: white;
}

.payment-tab:not(.active) {
    color: #6b7280;
}

.payment-tab:not(.active):hover {
    background: #f3f4f6;
    color: #111827;
}
</style>

<script>
let paymentInterval = null;

// Tab Switching
function switchTab(tabName) {
    // Update tabs
    document.querySelectorAll('.payment-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    
    // Update content
    document.querySelectorAll('.payment-tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    document.querySelector(`[data-content="${tabName}"]`).classList.remove('hidden');
}

function selectMethod(methodCode, methodName) {
    // Disable all buttons
    document.querySelectorAll('.payment-method-btn').forEach(btn => {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    });

    // Show loading
    const display = document.getElementById('payment-display');
    const content = document.getElementById('payment-content');
    
    content.innerHTML = `
        <div class="text-center py-12">
            <div class="relative">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200 border-t-telu-red mx-auto"></div>
            </div>
            <p class="text-gray-700 font-medium mt-6 text-lg">Membuat pembayaran...</p>
            <p class="text-gray-500 text-sm mt-2">Mohon tunggu sebentar</p>
        </div>
    `;
    display.classList.remove('hidden');
    
    // Smooth scroll to payment display
    display.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // Call API to create charge
    fetch('{{ route("payment.charge", $payment) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ method: methodCode })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showPaymentUI(methodCode, methodName, data.data);
            startPaymentPolling();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuat Pembayaran',
                text: data.message,
                confirmButtonColor: '#EC1C25'
            }).then(() => location.reload());
        }
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: 'Mohon coba lagi dalam beberapa saat',
            confirmButtonColor: '#EC1C25'
        }).then(() => location.reload());
    });
}

function showPaymentUI(method, methodName, data) {
    const content = document.getElementById('payment-content');
    
    if (method === 'qris') {
        const qrUrl = data.actions?.find(a => a.name === 'generate-qr-code')?.url || '';
        content.innerHTML = `
            <div class="text-center max-w-md mx-auto">
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Scan QRIS</h3>
                    <p class="text-gray-600 mb-6">Gunakan aplikasi pembayaran favorit Anda</p>
                </div>
                
                <div class="inline-block p-6 bg-white border-4 border-gray-200 rounded-2xl shadow-lg mb-6">
                    <img src="${qrUrl}" alt="QRIS Code" class="w-64 h-64 mx-auto">
                </div>
                
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-5 mb-6">
                    <p class="text-sm text-gray-700 mb-2">Total Bayar</p>
                    <p class="text-3xl font-bold text-telu-red">Rp${formatNumber(data.gross_amount)}</p>
                    <p class="text-xs text-gray-500 mt-3">
                        Berlaku hingga: ${formatTimestamp(data.expiry_time)}
                    </p>
                </div>
                
                <div class="flex items-center justify-center gap-2 text-sm">
                    <div class="animate-pulse w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-gray-600 font-medium">Menunggu pembayaran...</span>
                </div>
            </div>
        `;
    } else if (method.includes('_va')) {
        const bank = method.replace('_va', '').toUpperCase();
        const vaNumber = data.va_numbers?.[0]?.va_number || data.permata_va_number || 'N/A';
        
        content.innerHTML = `
            <div class="max-w-md mx-auto">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Transfer ke ${bank}</h3>
                    <p class="text-gray-600">Virtual Account Number</p>
                </div>
                
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 mb-6 border border-blue-200">
                    <p class="text-sm text-blue-700 font-medium mb-2">Nomor Virtual Account:</p>
                    <div class="flex items-center justify-between bg-white rounded-xl p-4 shadow-sm">
                        <span class="text-2xl sm:text-3xl font-mono font-bold text-gray-900 tracking-wider" id="va-number">${vaNumber}</span>
                        <button onclick="copyVA()" class="ml-4 px-4 py-2 bg-telu-red text-white text-sm font-bold rounded-lg hover:bg-red-700 transition-colors flex-shrink-0">
                            Salin
                        </button>
                    </div>
                </div>
                
                <div class="bg-amber-50 border-2 border-amber-300 rounded-xl p-5 mb-6">
                    <p class="text-sm text-amber-900 font-medium mb-2">
                        💳 Total Transfer (harus EXACT):
                    </p>
                    <p class="text-3xl font-bold text-amber-900">Rp${formatNumber(data.gross_amount)}</p>
                    <p class="text-xs text-amber-700 mt-2">
                        ⏰ Berlaku hingga: ${formatTimestamp(data.expiry_time)}
                    </p>
                </div>
                
                <div class="flex items-center justify-center gap-2 text-sm">
                    <div class="animate-pulse w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-gray-600 font-medium">Menunggu pembayaran...</span>
                </div>
            </div>
        `;
    } else {
        // E-wallet
        const deeplink = data.actions?.find(a => a.name === 'deeplink-redirect')?.url || '#';
        
        content.innerHTML = `
            <div class="text-center max-w-md mx-auto">
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Bayar dengan ${methodName}</h3>
                    <p class="text-gray-600 mb-8">Klik tombol di bawah untuk melanjutkan ke aplikasi</p>
                </div>
                
                <a href="${deeplink}" target="_blank" 
                   class="inline-block px-8 py-4 bg-gradient-to-r from-telu-red to-red-600 text-white font-bold rounded-xl hover:shadow-lg transition-all transform hover:scale-105 mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Buka ${methodName}
                    </span>
                </a>
                
                <div class="bg-gray-50 rounded-xl p-5 mb-6">
                    <p class="text-sm text-gray-700 mb-2">Total Bayar</p>
                    <p class="text-3xl font-bold text-telu-red">Rp${formatNumber(data.gross_amount)}</p>
                </div>
                
                <div class="flex items-center justify-center gap-2 text-sm">
                    <div class="animate-pulse w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-gray-600 font-medium">Menunggu pembayaran...</span>
                </div>
            </div>
        `;
    }
}

function copyVA() {
    const vaNumber = document.getElementById('va-number').textContent.trim();
    navigator.clipboard.writeText(vaNumber).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Nomor VA berhasil disalin',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    });
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function formatTimestamp(timestamp) {
    if (!timestamp) return 'N/A';
    const date = new Date(timestamp);
    return date.toLocaleString('id-ID', { 
        day: '2-digit', 
        month: 'long', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    }) + ' WIB';
}

function startPaymentPolling() {
    paymentInterval = setInterval(() => {
        fetch('{{ route("payment.status", $payment) }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'settlement' || data.status === 'capture') {
                clearInterval(paymentInterval);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil!',
                    text: 'Pesanan Anda sedang diproses oleh penjual.',
                    confirmButtonColor: '#EC1C25',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = '{{ route("orders.index") }}';
                });
            }
        })
        .catch(err => console.error('Polling error:', err));
    }, 5000);
}

// Clear interval on page unload
window.addEventListener('beforeunload', () => {
    if (paymentInterval) clearInterval(paymentInterval);
});
</script>
@endsection
