@extends('layouts.app')

@section('content')
<div
    class="w-full max-w-md bg-white border border-[var(--tc-card-border)] rounded-[24px] tc-card p-6 md:p-8 text-center"
>
    <h1 class="text-[1.25rem] font-semibold text-[var(--tc-text-main)] mb-2">
        Tel-U Consign
    </h1>

    <p class="text-[0.9rem] text-[var(--tc-text-dim)] mb-6">
        Selamat datang di marketplace konsignasi Tel-U.
    </p>

    <div class="flex flex-col gap-3">
        <a
            href="{{ route('login.form') }}"
            class="w-full rounded-md bg-[var(--tc-btn-bg)] hover:bg-[var(--tc-btn-bg-hover)]
                   text-white font-medium py-3 text-[0.95rem] transition-colors text-center"
        >
            Masuk
        </a>

        <a
            href="{{ route('register.form') }}"
            class="w-full rounded-md border border-[var(--tc-btn-bg)]
                   text-[var(--tc-btn-bg)] font-medium py-3 text-[0.95rem] transition-colors
                   hover:bg-[var(--tc-btn-bg)] hover:text-white text-center"
        >
            Daftar
        </a>
    </div>
</div>
@endsection
