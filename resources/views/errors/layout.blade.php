<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Telu Consignment</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'telu-red': '#EC1C25',
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-800 h-screen flex flex-col overflow-hidden relative">

    <!-- Decorative Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[20%] -right-[10%] w-[50%] h-[50%] bg-red-50 rounded-full blur-[100px] opacity-60"></div>
        <div class="absolute top-[40%] -left-[10%] w-[40%] h-[40%] bg-blue-50 rounded-full blur-[100px] opacity-60"></div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center p-6 relative z-10">
        <div class="max-w-xl w-full text-center">
            <!-- Logo -->
            <div class="mb-8 animate-fade-in-down">
                <a href="{{ url('/') }}" class="inline-block hover:scale-105 transition-transform duration-300 no-underline">
                   <h1 class="text-4xl font-black tracking-tight text-gray-900">
                        <span class="text-[#EC1C25]">Telu</span>Consign
                   </h1>
                </a>
            </div>

            <!-- Error Content -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-8 md:p-12 shadow-2xl border border-white/50 animate-fade-in-up">
                
                <div class="mb-6 relative">
                    <h1 class="text-[150px] font-black leading-none text-transparent bg-clip-text bg-gradient-to-b from-[#EC1C25]/20 to-transparent select-none absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 -z-10">
                        @yield('code')
                    </h1>
                    <div class="text-[#EC1C25]">
                        @yield('image')
                    </div>
                </div>

                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">
                    @yield('message')
                </h2>

                <p class="text-gray-500 text-lg mb-10 leading-relaxed max-w-md mx-auto">
                    @yield('description')
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ url('/') }}" class="w-full sm:w-auto px-8 py-3.5 bg-[#EC1C25] hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl shadow-red-200 transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Ke Beranda
                    </a>
                    
                    @hasSection('back')
                        <button onclick="history.back()" class="w-full sm:w-auto px-8 py-3.5 bg-white hover:bg-gray-50 text-gray-700 font-bold border-2 border-gray-100 rounded-xl transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali
                        </button>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-sm text-gray-400 font-medium">
                &copy; {{ date('Y') }} Telu Consignment. All rights reserved.
            </div>
        </div>
    </div>

</body>
</html>
