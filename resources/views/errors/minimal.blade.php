@extends('errors::layout')

@section('title', 'Terjadi Kesalahan')
@section('code', $exception->getStatusCode())

@section('image')
<svg class="w-32 h-32 mx-auto text-[#EC1C25]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
</svg>
@endsection

@section('message', $exception->getMessage() ?: 'Terjadi Kesalahan')

@section('description', 'Mohon maaf atas ketidaknyamanan ini.')

@section('back', true)
