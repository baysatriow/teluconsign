@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-3 tracking-tight">Selesaikan Pembayaran</h1>
            <p class="text-gray-500 text-lg">Pilih metode pembayaran yang Anda inginkan</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Order Summary - Sidebar -->
            <div class="lg:col-span-1 order-2 lg:order-1">
                <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-6 sticky top-8 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="bg-red-50 text-[#EC1C25] p-1.5 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </span>
                        Ringkasan Pesanan
                    </h3>
                    
                    <div class="space-y-4 text-sm mb-6">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-500 font-medium">Kode Pembayaran</span>
                            </div>
                            <span class="font-mono text-base font-bold text-gray-900 tracking-wider">{{ $payment->provider_order_id }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-600">Jumlah Item</span>
                            <span class="font-bold text-gray-900">{{ $relatedOrders->sum(fn($o) => $o->items->count()) }} produk</span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-600">Status</span>
                            @php
                                $statusBadge = match($payment->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'settlement' => 'bg-green-100 text-green-800 border-green-200',
                                    'expire' => 'bg-red-100 text-red-800 border-red-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg border {{ $statusBadge }}">
                                {{ strtoupper($payment->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-900 font-bold">Total Tagihan</span>
                            <span class="text-2xl font-extrabold text-[#EC1C25]">Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-100">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                <strong>Penting:</strong> Selesaikan pembayaran dalam <strong class="text-amber-900">24 jam</strong>. Pesanan akan otomatis dibatalkan jika melewati batas waktu.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods - Main Content -->
            <div class="lg:col-span-2 order-1 lg:order-2">
                
                @if($payment->method_code)
                    <!-- PAYMENT ALREADY SELECTED -->
                    <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-8 border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-green-50 rounded-full blur-3xl transform translate-x-10 -translate-y-10"></div>

                        <div class="relative z-10 flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
                            <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center shadow-inner">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Menunggu Pembayaran</h3>
                                <p class="text-sm text-gray-500">Metode: <span class="font-semibold text-gray-700">{{ strtoupper(str_replace('_', ' ', $payment->method_code)) }}</span></p>
                            </div>
                        </div>
                        
                        <div id="payment-display">
                            <div id="payment-content">
                                <div class="text-center py-12">
                                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-100 border-t-[#EC1C25] mx-auto mb-4"></div>
                                    <p class="text-gray-500 font-medium">Memuat detail pembayaran...</p>
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
                    <div id="payment-method-selection" class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-6 sm:p-8 border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Pilih Metode Pembayaran</h3>
                        
                        <!-- Tab Navigation (Modern Pills) -->
                        <div class="flex p-1 bg-gray-100 rounded-xl mb-8 overflow-x-auto">
                            <button onclick="switchTab('qr')" class="payment-tab flex-1 py-2.5 px-4 text-sm font-bold rounded-lg transition-all whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:ring-offset-2 active" data-tab="qr">
                                QR Code
                            </button>
                            <button onclick="switchTab('bank')" class="payment-tab flex-1 py-2.5 px-4 text-sm font-bold rounded-lg transition-all whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:ring-offset-2" data-tab="bank">
                                Transfer Bank
                            </button>
                            <button onclick="switchTab('ewallet')" class="payment-tab flex-1 py-2.5 px-4 text-sm font-bold rounded-lg transition-all whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:ring-offset-2" data-tab="ewallet">
                                E-Wallet
                            </button>
                        </div>

                        <!-- QR Code Methods -->
                        <div class="payment-tab-content animate-fade-in-up" data-content="qr">
                            <div class="grid grid-cols-1 gap-3">
                                <button onclick="selectMethod('qris', 'QRIS - Semua E-Wallet')" 
                                        class="payment-method-btn group flex items-center gap-5 p-5 border border-gray-200 rounded-2xl hover:border-[#EC1C25] hover:bg-red-50/30 hover:shadow-lg transition-all text-left w-full">
                                    <div class="w-16 h-16 bg-white rounded-xl border border-gray-100 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Logo_QRIS.svg/1200px-Logo_QRIS.svg.png" class="h-8 object-contain">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <p class="font-bold text-gray-900 text-lg">QRIS</p>
                                            <span class="text-xs font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Instant</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-3">Scan kode QR yang muncul dengan aplikasi E-Wallet atau Mobile Banking.</p>
                                        <div class="flex gap-2">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Gopay_logo.svg/2560px-Gopay_logo.svg.png" class="h-4 object-contain opacity-60">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Logo_ovo_purple.svg/2560px-Logo_ovo_purple.svg.png" class="h-4 object-contain opacity-60">
                                            <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhCgA6jJt23sUvbV4zJg8tJkM8vT9Jz7Xq6J5K4L3N2O1P0Q9R8S7T6U5V4W3X2Y1Z0a9B8c7d6e5f4g3h2i1j0k9l8m7n6o5p4q3r2s1t0u/s1600/Dana%20Logo.png" class="h-4 object-contain opacity-60">
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 group-hover:border-[#EC1C25] group-hover:bg-[#EC1C25] flex items-center justify-center transition-all">
                                        <svg class="w-4 h-4 text-white opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Virtual Account Methods -->
                        <div class="payment-tab-content hidden animate-fade-in-up" data-content="bank">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($methods as $method)
                                    @if($method['category'] === 'bank_transfer')
                                    <button onclick="selectMethod('{{ $method['code'] }}', '{{ $method['name'] }}')" 
                                            class="payment-method-btn group flex items-center gap-4 p-4 border border-gray-200 rounded-xl hover:border-[#EC1C25] hover:bg-red-50/30 hover:shadow-md transition-all text-left w-full">
                                        <div class="w-12 h-12 bg-gray-50 rounded-lg border border-gray-100 flex items-center justify-center group-hover:scale-105 transition-transform overflow-hidden">
                                            <!-- Simple Bank Logo Fallback -->
                                            <span class="text-xs font-black text-gray-700">{{ substr(strtoupper(str_replace('_va', '', $method['code'])), 0, 4) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-bold text-gray-900">{{ str_replace(' Virtual Account', '', $method['name']) }}</p>
                                            <p class="text-xs text-gray-500">Virtual Account</p>
                                        </div>
                                         <svg class="w-5 h-5 text-gray-300 group-hover:text-[#EC1C25] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- E-Wallet Methods -->
                        <div class="payment-tab-content hidden animate-fade-in-up" data-content="ewallet">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($methods as $method)
                                    @if($method['category'] === 'ewallet')
                                    <button onclick="selectMethod('{{ $method['code'] }}', '{{ $method['name'] }}')" 
                                            class="payment-method-btn group flex items-center gap-4 p-4 border border-gray-200 rounded-xl hover:border-[#EC1C25] hover:bg-red-50/30 hover:shadow-md transition-all text-left w-full">
                                        <div class="w-12 h-12 bg-gray-50 rounded-lg border border-gray-100 flex items-center justify-center group-hover:scale-105 transition-transform overflow-hidden">
                                            <span class="text-xs font-black text-gray-700">{{ substr($method['name'], 0, 4) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-bold text-gray-900">{{ $method['name'] }}</p>
                                            <p class="text-xs text-gray-500">E-Wallet Instant</p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-300 group-hover:text-[#EC1C25] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Payment Display Area -->
                    <div id="payment-display" class="mt-8 bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-100 hidden scroll-mt-24">
                        <div id="payment-content"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.payment-tab.active {
    background-color: white;
    color: #EC1C25;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.payment-tab:not(.active) {
    color: #6b7280;
    background-color: transparent;
}

.payment-tab:not(.active):hover {
    color: #111827;
    background-color: rgba(255, 255, 255, 0.5);
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
            document.getElementById('payment-method-selection').classList.add('hidden');
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
