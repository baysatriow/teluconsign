@extends('layouts.app')

@section('content')
<div
    class="w-full max-w-md bg-white border border-[var(--tc-card-border)] rounded-[24px] tc-card p-6 md:p-8"
>
    <div class="text-center text-[1.25rem] font-semibold text-[var(--tc-text-main)] mb-6">
        Masuk Akun Tel-U Consign
    </div>

    <form action="{{ route('login.submit') }}" method="POST" novalidate>
        @csrf

        {{-- Email --}}
        <div class="mb-4">
            <input
                id="email"
                name="email"
                type="email"
                required
                placeholder="Email"
                value="{{ old('email') }}"
                class="w-full rounded-md border border-[var(--tc-input-border)] px-3 py-3 text-[0.95rem]
                       text-[var(--tc-text-main)] placeholder-[var(--tc-text-dim)]
                       focus:outline-none focus:ring-4 focus:ring-[rgba(61,76,103,.15)]
                       focus:border-[var(--tc-input-border)]
                       @error('email') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
            />
            @error('email')
                <p class="text-red-500 text-[0.8rem] mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-2">
            <input
                id="password"
                name="password"
                type="password"
                required
                placeholder="Kata Sandi"
                class="w-full rounded-md border border-[var(--tc-input-border)] px-3 py-3 text-[0.95rem]
                       text-[var(--tc-text-main)] placeholder-[var(--tc-text-dim)]
                       focus:outline-none focus:ring-4 focus:ring-[rgba(61,76,103,.15)]
                       focus:border-[var(--tc-input-border)]
                       @error('password') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
            />
            @error('password')
                <p class="text-red-500 text-[0.8rem] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="text-right mb-6">
            <a href="#" class="text-[0.75rem] underline text-[var(--tc-text-main)] font-medium">
                Lupa Kata Sandi
            </a>
        </div>

        {{-- Tombol Masuk --}}
        <div class="mb-4">
            <button
                type="submit"
                class="w-full rounded-md bg-[var(--tc-btn-bg)] hover:bg-[var(--tc-btn-bg-hover)]
                       text-white font-medium py-3 text-[0.95rem] transition-colors text-center"
            >
                Masuk
            </button>
        </div>

        {{-- Footnote --}}
        <div class="text-center text-[0.8rem] text-[var(--tc-text-dim)]">
            Belum punya akun?
            <a href="{{ route('register.form') }}" class="text-[var(--tc-text-main)] font-medium underline">
                Daftar
            </a>
        </div>
    </form>
</div>
@endsection
