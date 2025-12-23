@extends('errors::layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')

@section('image')
<svg class="w-32 h-32 mx-auto text-[#EC1C25]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
</svg>
@endsection

@section('message', 'Oops! Halaman Hilang')

@section('description')
Sepertinya halaman yang Anda cari sudah tidak ada, dipindahkan, atau link yang Anda tuju salah alamat.
@endsection

@section('back', true)
