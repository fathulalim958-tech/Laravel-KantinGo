<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') · KantinGO UNPAM</title>
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
        .sidebar-glass {
            background: linear-gradient(180deg, #3d1d11 0%, #220e06 100%);
        }
        .sidebar-glass::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-glass::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-glass::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.25);
            border-radius: 9999px;
        }
        .sidebar-glass::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.45);
        }
        body {
            background-color: #f8fafc;
            min-height: 100vh;
        }
    </style>
</head>
<body class="text-stone-800 antialiased font-sans">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar (Fixed / Sticky on scroll) -->
        <aside class="sidebar-glass text-white w-full md:w-64 shrink-0 flex flex-col justify-between shadow-2xl z-20 md:sticky md:top-0 md:h-screen md:overflow-y-auto">
            <div>
                <!-- Brand -->
                <div class="p-6 border-b border-white/10 flex items-center gap-3">
                    <img src="{{ asset('assets/kantingo-logo-transparent-light.png') }}" alt="Logo" class="h-10 w-auto">
                    <div>
                        <span class="text-lg font-black tracking-tight text-amber-400 block leading-tight">Kantin<span class="text-amber-200">GO</span></span>
                        <span class="text-[10px] font-semibold tracking-wider text-amber-300 uppercase">Admin UNPAM</span>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="p-4 space-y-1 text-sm font-medium">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Dashboard Utama</span>
                    </a>

                    <a href="{{ route('admin.canteens.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.canteens*') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Kelola Kantin</span>
                    </a>

                    <a href="{{ route('admin.sellers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.sellers*') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Kelola Penjual</span>
                    </a>

                    <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.customers*') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Kelola Pelanggan</span>
                    </a>

                    <a href="{{ route('admin.transactions.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.transactions*') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <span>Monitoring Transaksi</span>
                    </a>

                    <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.activity-logs*') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Log Aktivitas (Audit)</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <span class="text-[10px] font-bold text-amber-400/60 uppercase tracking-wider px-3">Layanan Publik</span>
                    </div>

                    <a href="{{ route('customer.canteens') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-stone-300 hover:bg-white/5 hover:text-white transition-colors">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        <span>Lihat Portal Pembeli ↗</span>
                    </a>
                </nav>
            </div>

            <!-- Profile & Logout -->
            <div class="p-4 border-t border-white/10 shrink-0">
                <div class="flex items-center gap-3 px-2 py-2">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover border border-amber-400/40">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-amber-300 truncate">Administrator Platform</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 text-xs font-semibold border border-rose-500/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="bg-white border-b border-stone-200 px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-black text-stone-900">@yield('page_title', 'Dashboard')</h1>
                    <p class="text-xs text-stone-500">@yield('page_subtitle', 'Pusat Kendali Platform KantinGO Universitas Pamulang')</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-stone-600 bg-stone-100 px-3 py-1.5 rounded-full border border-stone-200">
                        📅 {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </header>

            <!-- Main Body -->
            <main class="flex-1 p-6 max-w-7xl w-full mx-auto space-y-6">
                <!-- Flash Alerts -->
                @if (session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
