<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>{{ $title ?? 'Tel-U Consign' }}</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Font Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />

    <style>
        :root {
            --tc-bg-soft: #f8f9fc;
            --tc-card-border: #cfd4e3;
            --tc-input-border: #3d4c67;
            --tc-text-main: #2f3a4f;
            --tc-text-dim: #6c758a;
            --tc-btn-bg: #3d4c67;
            --tc-btn-bg-hover: #323f55;
        }

        .tc-card {
            box-shadow:
                0 30px 60px rgba(26,32,53,.08),
                0 3px 8px rgba(26,32,53,.04);
        }

        /* SweetAlert2 custom style */
        .tc-swal {
            border-radius: 1rem !important;
            padding: 1.5rem 1.25rem 1.25rem !important;
            border: 1px solid var(--tc-card-border) !important;
            box-shadow:
                0 30px 60px rgba(26,32,53,.18),
                0 3px 8px rgba(26,32,53,.08) !important;
            font-family: 'Poppins', system-ui, sans-serif;
        }
        .tc-swal-title {
            font-size: 1.05rem !important;
            font-weight: 600 !important;
            color: var(--tc-text-main) !important;
            line-height: 1.4 !important;
        }
        .tc-swal-text {
            font-size: .9rem !important;
            font-weight: 400 !important;
            color: var(--tc-text-dim) !important;
            margin-top: .5rem !important;
        }
        .tc-swal-ok {
            background-color: var(--tc-btn-bg) !important;
            border-radius: .5rem !important;
            font-size: .9rem !important;
            font-weight: 500 !important;
            padding: .6rem 1rem !important;
        }
        .tc-swal-ok:hover {
            background-color: var(--tc-btn-bg-hover) !important;
        }
        .swal2-icon.swal2-success {
            border-color: #3d4c67 !important;
            color: #3d4c67 !important;
        }
        .swal2-icon.swal2-error {
            border-color: #dc3545 !important;
            color: #dc3545 !important;
        }
    </style>

    @stack('head')
</head>

<body
    class="min-h-screen flex items-center justify-center p-4 font-[Poppins] text-[var(--tc-text-main)]"
    style="background-color:var(--tc-bg-soft);
           background-image:url('/images/bg-pattern.svg');
           background-size:300px;
           background-repeat:repeat;"
>
    {{-- page content --}}
    {{ $slot ?? '' }}
    @yield('content')

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
        Swal.fire({
            customClass: {
                popup: 'tc-swal',
                title: 'tc-swal-title',
                htmlContainer: 'tc-swal-text',
                confirmButton: 'tc-swal-ok',
            },
            icon: 'success',
            title: 'Berhasil ✅',
            html: @json(session('success')),
            confirmButtonText: 'OK'
        });
        @endif

        @if ($errors->any())
        Swal.fire({
            customClass: {
                popup: 'tc-swal',
                title: 'tc-swal-title',
                htmlContainer: 'tc-swal-text',
                confirmButton: 'tc-swal-ok',
            },
            icon: 'error',
            title: 'Oops 😕',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonText: 'Cek lagi'
        });
        @endif
    </script>

    @stack('scripts')
</body>
</html>
