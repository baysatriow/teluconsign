@extends('layouts.app')

@section('content')

    <div class="fixed inset-0 flex items-center justify-center overflow-hidden">

        <div class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('{{ asset('images/bg.png') }}'); opacity: 0.20;">
        </div>

        {{-- Card login di atas background --}}
        <div class="relative z-10 w-full max-w-md bg-white/90 backdrop-blur-sm
                       border border-[var(--tc-card-border)] rounded-[24px] tc-card p-6 md:p-8 shadow-lg">
            <div class="text-center text-[1.25rem] font-semibold text-[#EC1C25] mb-6">
                Masuk Akun Tel-U Consign
            </div>

            <form action="{{ route('login.submit') }}" method="POST" novalidate>
                @csrf

                {{-- Notif error global --}}
                @if ($errors->any())
                    <div class="mb-4 text-[0.8rem] text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Email --}}
                <div class="mb-4">
                    <input id="email" name="email" type="email" required placeholder="Email" value="{{ old('email') }}"
                        class="w-full rounded-md border border-[var(--tc-input-border)] px-3 py-3 text-[0.95rem]
                                   text-[var(--tc-text-main)] placeholder-[var(--tc-text-dim)]
                                   focus:outline-none focus:ring-4 focus:ring-[rgba(236,28,37,0.18)]
                                   focus:border-[var(--tc-input-border)]
                                   @error('email') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror" />
                    @error('email')
                        <p class="text-red-500 text-[0.8rem] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-2">
                    <input id="password" name="password" type="password" required placeholder="Kata Sandi" class="w-full rounded-md border border-[var(--tc-input-border)] px-3 py-3 text-[0.95rem]
                                   text-[var(--tc-text-main)] placeholder-[var(--tc-text-dim)]
                                   focus:outline-none focus:ring-4 focus:ring-[rgba(236,28,37,0.18)]
                                   focus:border-[var(--tc-input-border)]
                                   @error('password') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror" />
                    @error('password')
                        <p class="text-red-500 text-[0.8rem] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="text-right mb-6">
                    <a href="#" class="text-[0.75rem] underline text-[#EC1C25] font-medium">
                        Lupa Kata Sandi
                    </a>
                </div>

                {{-- Tombol Masuk --}}
                <div class="mb-4">
                    <button type="submit" class="w-full rounded-md bg-[#EC1C25] hover:bg-[#c0151d]
                                   text-white font-medium py-3 text-[0.95rem] transition-colors text-center">
                        Masuk
                    </button>
                </div>

                {{-- Footnote --}}
                <div class="text-center text-[0.8rem] text-[var(--tc-text-dim)]">
                    Belum punya akun?
                    <a href="{{ route('register.form') }}" class="text-[#EC1C25] font-medium underline">
                        Daftar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection