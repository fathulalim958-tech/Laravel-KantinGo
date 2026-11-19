<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Katalog Kantin') · KantinGO Universitas Pamulang</title>
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
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-header {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(245, 158, 11, 0.2);
        }
        body {
            background: radial-gradient(circle at top right, #fdf8f0, #faf3e8, #f5ead8);
            min-height: 100vh;
        }
    </style>
</head>
<body class="text-stone-800 antialiased font-sans flex flex-col min-h-screen">
    <!-- Navbar -->
    <header class="sticky top-0 z-40 glass-header shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <!-- Brand -->
            <a href="{{ route('customer.canteens') }}" class="flex items-center gap-3 shrink-0 group">
                <img src="{{ asset('assets/kantingo-logo-transparent.png') }}" alt="Logo" class="h-10 w-auto transition-transform group-hover:scale-105">
                <div class="hidden sm:block">
                    <span class="text-xl font-black tracking-tight text-amber-900 block leading-none">Kantin<span class="text-amber-600">GO</span></span>
                    <span class="text-[10px] font-bold tracking-wider text-amber-700 uppercase">Universitas Pamulang</span>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="flex items-center gap-1 sm:gap-2">
                <a href="{{ route('customer.canteens') }}" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition-colors {{ request()->routeIs('customer.canteens*') ? 'bg-amber-100 text-amber-900' : 'text-stone-600 hover:text-amber-800 hover:bg-amber-50' }}">
                    🏪 Jelajahi Kantin
                </a>
                <a href="{{ route('customer.orders.index') }}" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition-colors {{ request()->routeIs('customer.orders*') ? 'bg-amber-100 text-amber-900' : 'text-stone-600 hover:text-amber-800 hover:bg-amber-50' }}">
                    📋 Pesanan Saya
                </a>
                <a href="{{ route('customer.cart') }}" class="relative px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition-colors {{ request()->routeIs('customer.cart*') ? 'bg-amber-600 text-white' : 'text-stone-600 hover:text-amber-800 hover:bg-amber-50' }}">
                    🛒 Keranjang
                    @php
                        $cartQty = array_sum(session()->get('cart', []));
                    @endphp
                    @if($cartQty > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-rose-600 text-white text-[11px] font-black flex items-center justify-center shadow-md animate-pulse">
                            {{ $cartQty }}
                        </span>
                    @endif
                </a>
            </nav>

            <!-- User Pill & Logout -->
            <div class="flex items-center gap-3">
                @auth
                    <div class="hidden md:flex items-center gap-2 bg-white/80 py-1.5 px-3 rounded-2xl border border-stone-200 shadow-sm text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="font-bold text-stone-800">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] uppercase font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200">
                            {{ auth()->user()->user_type ?? 'Civitas' }}
                        </span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-stone-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors" title="Keluar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm transition-colors">
                        Masuk / Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Flash Alerts -->
        @if (session('success'))
            <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-5 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">{{ session('warning') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white/80 border-t border-amber-200/50 py-6 mt-12 text-center text-xs text-stone-500">
        <div class="max-w-7xl mx-auto px-4 space-y-1">
            <p><strong>KantinGO Universitas Pamulang</strong> · Platform Pemesanan Makanan & Minuman Multi-Kantin Kampus.</p>
            <p class="text-[11px] text-stone-400">Pesan tanpa antre · Ambil saat siap · Didukung oleh Civitas Akademika UNPAM</p>
        </div>
    </footer>
</body>
</html>
