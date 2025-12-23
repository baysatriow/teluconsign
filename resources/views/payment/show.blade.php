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
                        
                        {{-- Demo Payment Button (Sandbox Only) --}}
                        @if($isSandbox ?? false)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <button onclick="demoPayment()" id="btn-demo-pay" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Bayar Sekarang (Demo - Sandbox)
                            </button>
                            <p class="text-xs text-gray-400 text-center mt-2">Tombol ini hanya muncul di mode sandbox untuk testing</p>
                        </div>
                        @endif
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
const isSandbox = @json($isSandbox ?? false); // Pass sandbox status to JS

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
    // Disable all buttons visual feedback immediately
    const btn = event.currentTarget;
    const oldHtml = btn.innerHTML;
    
    // Show loading on the button itself first - simple text replacement
    btn.innerHTML = `<div class="flex items-center justify-center w-full font-bold text-gray-500">Memproses...</div>`;
    
    document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.add('opacity-50', 'pointer-events-none'));

    // Show loading in display area
    const display = document.getElementById('payment-display');
    
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
            // Hide selection area entirely
            document.getElementById('payment-method-selection').classList.add('hidden');
            
            // Show payment UI
            display.classList.remove('hidden');
            showPaymentUI(methodCode, methodName, data.data);
            
            // Show demo button if sandbox
            if(isSandbox) {
                // Check if container exists, if not create it
                let demoContainer = document.getElementById('demo-payment-container');
                if(!demoContainer) {
                    demoContainer = document.createElement('div');
                    demoContainer.id = 'demo-payment-container';
                    demoContainer.className = 'mt-6 pt-6 border-t border-gray-100';
                    display.appendChild(demoContainer);
                }
                
                demoContainer.innerHTML = `
                    <button onclick="demoPayment()" id="btn-demo-pay" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Bayar Sekarang (Demo - Sandbox)
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-2">Tombol ini hanya muncul di mode sandbox untuk testing</p>
                `;
                demoContainer.classList.remove('hidden');
            }

            startPaymentPolling();
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            // Revert button state
             btn.innerHTML = oldHtml;
             document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.remove('opacity-50', 'pointer-events-none'));
             
            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuat Pembayaran',
                text: data.message,
                confirmButtonText: 'Tutup',
                customClass: {
                    confirmButton: 'bg-red-600 text-white font-bold py-2 px-6 rounded-lg'
                },
                buttonsStyling: false
            });
        }
    })
    .catch(err => {
        console.error(err);
         btn.innerHTML = oldHtml;
         document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.remove('opacity-50', 'pointer-events-none'));
         
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: 'Mohon coba lagi dalam beberapa saat',
            confirmButtonText: 'Tutup',
             customClass: {
                    confirmButton: 'bg-red-600 text-white font-bold py-2 px-6 rounded-lg'
                },
            buttonsStyling: false
        });
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
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-50 rounded-full mb-4 ring-8 ring-blue-50/50">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">Transfer ke ${bank}</h3>
                    <p class="text-gray-500 text-sm">Selesaikan pembayaran ke nomor Virtual Account di bawah</p>
                </div>
                
                <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-200 mb-6 relative overflow-hidden group hover:border-blue-300 transition-colors">
                    <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-blue-900" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4h16v16H4z" fill="none"/><path d="M20 2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 18H4V4h16v16z"/></svg>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor Virtual Account</label>
                            <button onclick="copyVA()" 
                                    class="flex items-center gap-1.5 px-3 py-1 bg-white border border-gray-200 hover:border-blue-500 text-gray-600 hover:text-blue-600 text-xs font-bold rounded-lg transition-all shadow-sm hover:shadow active:scale-95"
                                    title="Salin Nomor VA">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                <span>Salin</span>
                            </button>
                        </div>
                        <span class="text-3xl sm:text-4xl font-mono font-bold text-gray-900 tracking-tight block select-all" id="va-number">${vaNumber}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-3 italic">*Cek otomatis dalam 5-10 menit</p>
                </div>
                
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm text-amber-800 font-medium">Total Transfer</p>
                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Harus Persis</span>
                    </div>
                    <p class="text-3xl font-extrabold text-gray-900 tracking-tight">Rp${formatNumber(data.gross_amount)}</p>
                    <div class="mt-4 flex items-center gap-2 text-xs text-amber-700 bg-amber-100/50 p-2 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Bayar sebelum: <span class="font-bold">${formatTimestamp(data.expiry_time)}</span>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-full border border-gray-200">
                        <span class="relative flex h-2.5 w-2.5">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                        </span>
                        <span class="text-sm font-medium text-gray-600">Menunggu pembayaran otomatis...</span>
                    </div>
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
                   class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white border-2 border-[#EC1C25] text-[#EC1C25] font-bold rounded-xl hover:bg-[#EC1C25] hover:text-white transition-all transform hover:scale-105 mb-6 shadow-sm hover:shadow-lg w-full sm:w-auto">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    <span>Buka Aplikasi ${methodName}</span>
                </a>
                
                <div class="bg-gray-50 rounded-xl p-5 mb-6 border border-gray-100">
                    <p class="text-sm text-gray-700 mb-2 font-medium">Total Bayar</p>
                    <p class="text-3xl font-extrabold text-gray-900">Rp${formatNumber(data.gross_amount)}</p>
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

// Demo Payment (Sandbox Only)
// Demo Payment (Sandbox Only)
function demoPayment() {
    const btn = document.getElementById('btn-demo-pay');
    const originalText = btn.innerHTML;
    
    Swal.fire({
        title: '<h3 class="font-bold text-xl text-gray-900">Simulasi Pembayaran?</h3>',
        html: '<div class="text-gray-600 mb-2">Ini akan mensimulasikan pembayaran sukses.</div><div class="text-sm text-purple-600 font-medium bg-purple-50 p-2 rounded-lg">Gunakan hanya untuk testing (Sandbox Mode).</div>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#8B5CF6',
        cancelButtonColor: '#9CA3AF',
        confirmButtonText: 'Ya, Bayar Sekarang!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-2xl border border-gray-100 shadow-xl',
            confirmButton: 'bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transform transition hover:-translate-y-0.5',
            cancelButton: 'bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 px-6 rounded-xl transition',
            title: 'font-bold text-gray-900',
            htmlContainer: 'text-gray-600'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            fetch('{{ route("payment.demo", $payment) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Simulasi Dikirim!',
                        text: 'Menunggu webhook atau update langsung...',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: {
                             popup: 'rounded-2xl border border-green-100 shadow-xl',
                             title: 'font-bold text-gray-900'
                        }
                    });
                    
                    // Start polling for payment status
                    startPaymentPolling();
                    
                    // Wait a bit then check status immediately
                    setTimeout(() => {
                        window.location.href = '{{ route("orders.index") }}';
                    }, 2000);
                    
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan',
                        confirmButtonText: 'Tutup',
                        customClass: {
                            confirmButton: 'bg-red-600 text-white font-bold py-2 px-6 rounded-lg'
                        },
                        buttonsStyling: false
                    });
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem',
                     customClass: {
                            confirmButton: 'bg-red-600 text-white font-bold py-2 px-6 rounded-lg'
                        },
                     buttonsStyling: false
                });
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    });
}
</script>
@endsection
