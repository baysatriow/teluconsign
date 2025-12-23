@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute inset-0 z-0">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gray-50 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-white to-transparent"></div>
    </div>

    <div class="max-w-screen-xl mx-auto px-4 py-16 md:py-24 relative z-10 flex flex-col items-center text-center">
        
        <!-- Hero Badge -->
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 text-[#EC1C25] text-xs font-bold uppercase tracking-wider mb-8 animate-fade-in-down">
            <span class="w-2 h-2 rounded-full bg-[#EC1C25] animate-pulse"></span>
            Tel-U Consign Seller
        </span>

        <!-- Hero Title -->
        <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 mb-6 tracking-tight leading-tight animate-fade-in-up">
            Jualan Lebih <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#EC1C25] to-orange-500">Cuan</span> <br>
            di Kampus Sendiri
        </h1>

        <!-- Hero Description -->
        <p class="text-xl text-gray-500 max-w-2xl mb-10 leading-relaxed animate-fade-in-up delay-100">
            Platform jual beli eksklusif untuk mahasiswa Telkom University. 
            Tanpa biaya admin, terpercaya, dan langsung terhubung dengan ribuan pembeli potensial.
        </p>

        <!-- Main CTA -->
        <div class="animate-fade-in-up delay-200">
            <form action="{{ route('shop.register') }}" method="POST">
                @csrf
                <button type="submit" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white transition-all duration-200 bg-[#EC1C25] rounded-full focus:outline-none focus:ring-4 focus:ring-red-100 hover:bg-[#c4161e] shadow-xl hover:shadow-2xl hover:-translate-y-1 overflow-hidden">
                    <span class="mr-2">Buka Toko Gratis</span>
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    <div class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_2s_infinite] bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                </button>
            </form>
            <p class="mt-4 text-sm text-gray-400">
                Syarat & Ketentuan berlaku. Proses aktivasi instan.
            </p>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-24 text-left w-full animate-fade-in-up delay-300">
            <!-- Feature 1 -->
            <div class="group p-8 bg-white rounded-3xl border border-gray-100 shadow-lg hover:shadow-2xl hover:border-red-100 transition-all duration-300 hover:-translate-y-2">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-xl mb-3">0% Biaya Layanan</h3>
                <p class="text-gray-500 leading-relaxed">Keuntungan 100% milikmu. Platform tidak memotong komisi penjualan, sehingga harga lebih bersaing.</p>
            </div>

            <!-- Feature 2 -->
            <div class="group p-8 bg-white rounded-3xl border border-gray-100 shadow-lg hover:shadow-2xl hover:border-red-100 transition-all duration-300 hover:-translate-y-2">
                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-xl mb-3">Terverifikasi & Aman</h3>
                <p class="text-gray-500 leading-relaxed">Ekosistem khusus mahasiswa Telkom University. Transaksi aman dengan fitur verifikasi identitas.</p>
            </div>

            <!-- Feature 3 -->
            <div class="group p-8 bg-white rounded-3xl border border-gray-100 shadow-lg hover:shadow-2xl hover:border-red-100 transition-all duration-300 hover:-translate-y-2">
                <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-xl mb-3">Pasar Spesifik</h3>
                <p class="text-gray-500 leading-relaxed">Langsung targetkan barang daganganmu ke ribuan mahasiswa yang membutuhkan di sekitar kampus.</p>
            </div>
        </div>

        <!-- How it Works (Simple Steps) -->
        <div class="mt-24 w-full max-w-4xl">
            <h2 class="text-3xl font-bold text-gray-900 mb-12">Cara Mulai Berjualan</h2>
            <div class="flex flex-col md:flex-row justify-between items-center relative">
                <!-- Line -->
                <div class="hidden md:block absolute top-1/2 left-0 w-full h-1 bg-gray-100 -z-10 -translate-y-1/2"></div>

                <!-- Step 1 -->
                <div class="bg-white p-4 rounded-xl text-center w-full md:w-1/3 relative">
                    <div class="w-12 h-12 bg-[#EC1C25] text-white rounded-full flex items-center justify-center font-bold text-xl mx-auto mb-4 border-4 border-white shadow-lg">1</div>
                    <h4 class="font-bold text-gray-900">Aktivasi Toko</h4>
                    <p class="text-sm text-gray-500 mt-1">Klik tombol Buka Toko Gratis</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white p-4 rounded-xl text-center w-full md:w-1/3 relative mt-8 md:mt-0">
                    <div class="w-12 h-12 bg-[#EC1C25] text-white rounded-full flex items-center justify-center font-bold text-xl mx-auto mb-4 border-4 border-white shadow-lg">2</div>
                    <h4 class="font-bold text-gray-900">Lengkapi Profil</h4>
                    <p class="text-sm text-gray-500 mt-1">Atur alamat & rekening bank</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white p-4 rounded-xl text-center w-full md:w-1/3 relative mt-8 md:mt-0">
                    <div class="w-12 h-12 bg-[#EC1C25] text-white rounded-full flex items-center justify-center font-bold text-xl mx-auto mb-4 border-4 border-white shadow-lg">3</div>
                    <h4 class="font-bold text-gray-900">Upload Produk</h4>
                    <p class="text-sm text-gray-500 mt-1">Mulai posting barang jualanmu</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
