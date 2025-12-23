@extends('errors::layout')

@section('title', 'Terjadi Kesalahan Server')
@section('code', '500')

@section('image')
<svg class="w-32 h-32 mx-auto text-[#EC1C25]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
</svg>
@endsection

@section('message', 'Terjadi Kesalahan Sistem')

@section('description')
Mohon maaf, server kami sedang mengalami gangguan. Tim teknis kami sudah diberitahu dan sedang memperbaikinya.
@endsection

@section('back', true)
