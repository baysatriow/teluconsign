@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-16 flex flex-col items-center justify-center text-center">

    <div class="bg-red-50 p-6 rounded-full mb-6">
        <svg class="w-16 h-16 text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
    </div>

    <h1 class="text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Mulai Jualan di Tel-U Consign</h1>
    <p class="text-lg text-gray-500 max-w-2xl mb-8">
        Ubah barang bekasmu menjadi uang tunai. Jangkau ribuan mahasiswa Telkom University dengan mudah, aman, dan gratis.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12 text-left w-full max-w-4xl">
        <div class="p-6 border border-gray-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-all">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-4 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="font-bold text-gray-900 text-lg mb-2">Gratis Biaya Layanan</h3>
            <p class="text-sm text-gray-500">Nikmati keuntungan penuh dari hasil penjualanmu tanpa potongan admin yang memberatkan.</p>
        </div>
        <div class="p-6 border border-gray-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-all">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-4 text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="font-bold text-gray-900 text-lg mb-2">Verifikasi Mudah</h3>
            <p class="text-sm text-gray-500">Cukup gunakan akun SSO Telkom University kamu untuk mulai berjualan secara instan.</p>
        </div>
        <div class="p-6 border border-gray-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-all">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-4 text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h3 class="font-bold text-gray-900 text-lg mb-2">Komunitas Terpercaya</h3>
            <p class="text-sm text-gray-500">Bertransaksi dengan sesama mahasiswa dalam lingkungan kampus yang aman.</p>
        </div>
    </div>

    <form action="{{ route('shop.register') }}" method="POST">
        @csrf
        <button type="submit" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-lg px-10 py-4 shadow-xl hover:shadow-red-500/30 transition-all transform hover:-translate-y-1">
            Buka Toko Gratis Sekarang
        </button>
    </form>
    <p class="mt-4 text-sm text-gray-400">Dengan menekan tombol di atas, Anda menyetujui Syarat & Ketentuan Penjual.</p>

</div>
@endsection
