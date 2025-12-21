@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payment Gateway</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola koneksi Midtrans (Snap) untuk transaksi pembayaran.</p>
        </div>
        <div>
             @if($midtrans->is_active)
                <span class="bg-green-100 text-green-700 text-xs font-bold px-4 py-2 rounded-xl flex items-center gap-2 border border-green-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span> ACTIVE
                </span>
            @else
                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-4 py-2 rounded-xl border border-gray-200">INACTIVE</span>
            @endif
        </div>
    </div>
</div>

<div class="p-4 mb-6 text-blue-800 border border-blue-200 rounded-xl bg-blue-50">
    <div class="flex items-center">
        <svg class="flex-shrink-0 w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <span class="sr-only">Info</span>
        <h3 class="text-sm font-bold">Info Integrasi</h3>
    </div>
    <div class="mt-2 text-sm">
        Metode pembayaran (GoPay, VA, dll) dikelola langsung melalui <b>Dashboard Midtrans</b>. Snap akan otomatis menampilkan semua metode yang aktif pada akun merchant Anda. 
        <a href="https://dashboard.midtrans.com" target="_blank" class="text-blue-900 underline hover:no-underline font-bold ml-1">Buka Dashboard Midtrans &rarr;</a>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
    
    <!-- LEFT: CONFIGURATION -->
    <div class="space-y-8">
        <!-- Main Config Card -->
        <div class="bg-white rounded-2xl shadow-soft p-8 relative overflow-hidden border border-gray-100">
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/9d/Midtrans.png" class="w-32" alt="Midtrans">
            </div>
            
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                Konfigurasi API
            </h3>
            
            <form action="{{ route('admin.integrations.payment.update') }}" method="POST">
                @csrf @method('PATCH')
                
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Environment Mode</label>
                        <select name="mode" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-telu-red/20 focus:border-telu-red block w-full p-3 transition-all">
                            <option value="sandbox" {{ ($midtrans->meta_json['environment'] ?? '') == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                            <option value="production" {{ ($midtrans->meta_json['environment'] ?? '') == 'production' ? 'selected' : '' }}>Production (Live)</option>
                        </select>
                        <p class="mt-2 text-xs text-gray-500">Gunakan Sandbox untuk uji coba transaksi.</p>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Merchant ID</label>
                        <input type="text" name="merchant_id" value="{{ $midtrans->meta_json['merchant_id'] ?? '' }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-telu-red/20 focus:border-telu-red block w-full p-3 transition-all" placeholder="G-12345678">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Client Key</label>
                    <input type="text" name="client_key" value="{{ $midtrans->public_k ?? '' }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-telu-red/20 focus:border-telu-red block w-full p-3 font-mono transition-all" placeholder="SB-Mid-client-..." required>
                </div>

                <div class="mb-8">
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Server Key (Secret)</label>
                    <div class="relative">
                        <input type="password" name="server_key" value="{{ $midtrans->server_key ?? '' }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-telu-red/20 focus:border-telu-red block w-full p-3 font-mono transition-all" placeholder="SB-Mid-server-..." required>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="text-white bg-telu-red hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-100 font-bold rounded-xl text-sm px-6 py-3 text-center flex items-center gap-2 shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- RIGHT: SIMULATOR -->
    <div class="xl:col-span-1">
        <div class="bg-white rounded-2xl shadow-soft p-6 sticky top-28 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                Pembayaran Simulator
            </h3>
            <p class="text-sm text-gray-500 mb-6">Test konfigurasi Snap dengan membuat transaksi dummy.</p>
            
            <div class="space-y-5">
                <div class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-200">
                    <p class="text-[10px] text-gray-400 mb-1 font-bold uppercase tracking-wider">Client Key Status</p>
                    @if($midtrans->public_k)
                        <p class="text-xs font-mono text-green-600 truncate bg-green-50 p-2 rounded-lg border border-green-100 font-medium">{{ $midtrans->public_k }}</p>
                    @else
                         <p class="text-sm font-bold text-red-500 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span> Belum disetting
                        </p>
                    @endif
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase block mb-2">Nominal Test (IDR)</label>
                    <input type="number" id="test-amount" value="10000" class="w-full text-sm rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all p-3">
                </div>

                <div class="p-3 bg-yellow-50 border border-yellow-100 rounded-xl text-xs text-yellow-800">
                    <span class="font-bold">Note:</span> Pastikan Client Key dan Server Key sudah sesuai dengan dashboard Midtrans (Environment Sandbox/Production).
                </div>

                <button type="button" id="pay-button" class="w-full text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 font-bold rounded-xl text-sm px-5 py-4 shadow-lg shadow-blue-500/20 transform transition hover:-translate-y-1 flex justify-center items-center gap-2">
                    <span>TEST BAYAR SEKARANG</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
                <div id="result-json" class="hidden mt-4 p-4 bg-gray-900 text-green-400 text-xs font-mono rounded-xl overflow-x-auto max-h-40 border border-gray-800 shadow-inner"></div>
            </div>
        </div>
    </div>

</div>

<!-- MIDTRANS SNAP SCRIPT -->
@php
    $isProduction = ($midtrans->meta_json['environment'] ?? 'sandbox') === 'production';
    $snapUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp
<!-- Gunakan Client Key dari DB -->
<script src="{{ $snapUrl }}" data-client-key="{{ $midtrans->public_k }}"></script>

<script>
    const payButton = document.getElementById('pay-button');
    const resultDiv = document.getElementById('result-json');
    const amountInput = document.getElementById('test-amount');

    payButton.addEventListener('click', async function() {
        // Simple Validation
        if (!'{{ $midtrans->public_k }}') {
            Swal.fire({ icon: 'error', title: 'Client Key Hilang', text: 'Mohon isi dan simpan Client Key terlebih dahulu.' });
            return;
        }

        const originalText = payButton.innerHTML;
        payButton.innerHTML = 'Connecting...';
        payButton.disabled = true;
        resultDiv.classList.add('hidden');
        
        const amount = amountInput.value || 10000;

        try {
            // 1. Get Token via AJAX
            const response = await fetch("{{ route('admin.integrations.payment.test-token') }}?amount=" + amount);
            const data = await response.json();

            if(data.status === 'success') {
                payButton.innerHTML = 'Waiting Payment...';
                
                // 2. Open Snap Popup
                window.snap.pay(data.token, {
                    onSuccess: function(result){
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: "Pembayaran Test Berhasil!", customClass: { popup: 'rounded-2xl', confirmButton: 'bg-telu-red text-white px-4 py-2 rounded-xl' } });
                        resultDiv.innerHTML = JSON.stringify(result, null, 2);
                        resultDiv.classList.remove('hidden');
                        payButton.innerHTML = originalText;
                        payButton.disabled = false;
                    },
                    onPending: function(result){
                        Swal.fire({ icon: 'info', title: 'Pending', text: "Menunggu pembayaran...", customClass: { popup: 'rounded-2xl', confirmButton: 'bg-blue-600 text-white px-4 py-2 rounded-xl' } });
                        resultDiv.innerHTML = JSON.stringify(result, null, 2);
                        resultDiv.classList.remove('hidden');
                        payButton.innerHTML = originalText;
                        payButton.disabled = false;
                    },
                    onError: function(result){
                        Swal.fire({ icon: 'error', title: 'Gagal', text: "Pembayaran gagal!", customClass: { popup: 'rounded-2xl', confirmButton: 'bg-gray-800 text-white px-4 py-2 rounded-xl' } });
                        resultDiv.innerHTML = JSON.stringify(result, null, 2);
                        resultDiv.classList.remove('hidden');
                        payButton.innerHTML = originalText;
                        payButton.disabled = false;
                    },
                    onClose: function(){
                        payButton.innerHTML = originalText;
                        payButton.disabled = false;
                    }
                });

            } else {
                Swal.fire({ icon: 'error', title: 'Error Token', text: data.message });
                payButton.innerHTML = originalText;
                payButton.disabled = false;
            }

        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error Connection', text: error.message });
            payButton.innerHTML = originalText;
            payButton.disabled = false;
        }
    });
</script>
@endsection
