@extends('errors::layout')

@section('title', 'Akses Ditolak')
@section('code', '403')

@section('image')
<svg class="w-32 h-32 mx-auto text-[#EC1C25]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
</svg>
@endsection

@section('message', 'Akses Ditolak')

@section('description')
Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi admin jika Anda merasa ini adalah kesalahan.
@endsection

@section('back', true)
