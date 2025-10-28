@extends('layouts.app')

@section('content')
<div
    class="w-full max-w-md bg-white border border-[var(--tc-card-border)] rounded-[24px] tc-card p-6 md:p-8 text-center"
>
    <div class="text-[1.25rem] font-semibold text-[var(--tc-text-main)] mb-4">
        Dashboard
    </div>

    <p class="text-[1rem] font-medium text-[var(--tc-text-main)] mb-6">
        Selamat, berhasil login! 🎉
    </p>

    <form action="{{ route('logout') }}" method="POST" class="w-full">
        @csrf
        <button
            type="submit"
            class="w-full rounded-md bg-[var(--tc-btn-bg)] hover:bg-[var(--tc-btn-bg-hover)]
                   text-white font-medium py-3 text-[0.95rem] transition-colors text-center"
        >
            Logout
        </button>
    </form>
</div>
@endsection
