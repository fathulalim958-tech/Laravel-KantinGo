<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KantinGO') · Universitas Pamulang</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                            950: '#451a03',
                        },
                        unpam: {
                            blue: '#1e3a8a',
                            gold: '#fbbf24',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .glass-dark {
            background: rgba(30, 20, 15, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        body {
            background: radial-gradient(circle at top left, #fdf6ec, #fef1df, #fae2c5);
            min-height: 100vh;
        }
    </style>
</head>
<body class="text-stone-800 antialiased font-sans">
    <div class="min-h-screen flex flex-col justify-between p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <header class="max-w-7xl mx-auto w-full flex items-center justify-between py-2">
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('assets/kantingo-logo-transparent.png') }}" alt="Logo KantinGO" class="h-10 sm:h-12 w-auto object-contain transition-transform group-hover:scale-105">
                <div>
                    <span class="text-xl sm:text-2xl font-black tracking-tight text-amber-900 block leading-tight">Kantin<span class="text-amber-600">GO</span></span>
                    <span class="text-[10px] sm:text-xs font-semibold tracking-wider text-amber-700 uppercase">Universitas Pamulang</span>
                </div>
            </a>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Multi-Vendor Aktif
                </span>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center my-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="max-w-7xl mx-auto w-full text-center text-xs text-stone-500 py-3">
            <p>© {{ date('Y') }} <strong>KantinGO 2.0</strong> — Platform Pemesanan Makanan Terpadu Universitas Pamulang.</p>
            <p class="text-[11px] text-stone-400 mt-1">Multi-Vendor University Canteen System · Laravel & MySQL</p>
        </footer>
    </div>
</body>
</html>
