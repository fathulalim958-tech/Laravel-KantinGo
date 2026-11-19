<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Seller Dashboard') · KantinGO</title>
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
        .sidebar-seller {
            background: linear-gradient(180deg, #422006 0%, #291203 100%);
        }
        .sidebar-seller::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-seller::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-seller::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.25);
            border-radius: 9999px;
        }
        .sidebar-seller::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.45);
        }
        body {
            background-color: #faf5ee;
            min-height: 100vh;
        }
    </style>
</head>
<body class="text-stone-800 antialiased font-sans">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar (Fixed / Sticky on scroll) -->
        <aside class="sidebar-seller text-white w-full md:w-64 shrink-0 flex flex-col justify-between shadow-2xl z-20 md:sticky md:top-0 md:h-screen md:overflow-y-auto">
            <div>
                <!-- Brand -->
                <div class="p-6 border-b border-amber-900/50 flex items-center gap-3">
                    <img src="{{ asset('assets/kantingo-logo-transparent-light.png') }}" alt="Logo" class="h-10 w-auto">
                    <div>
                        <span class="text-lg font-black tracking-tight text-amber-400 block leading-tight">Kantin<span class="text-amber-200">GO</span></span>
                        <span class="text-[10px] font-bold tracking-wider text-amber-300 uppercase">Panel Penjual</span>
                    </div>
                </div>

                <!-- Canteen Profile Pill -->
                @php $myCanteen = auth()->user()->canteen; @endphp
                @if($myCanteen)
                    <div class="p-4 mx-3 my-3 rounded-2xl bg-amber-950/60 border border-amber-800/40 space-y-2">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $myCanteen->logo_url }}" alt="" class="w-8 h-8 rounded-lg object-contain bg-amber-900/50 p-1 border border-amber-700/40 shrink-0">
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-amber-200 truncate">{{ $myCanteen->name }}</h4>
                                <span class="text-[10px] text-amber-400/80 truncate block">📍 {{ $myCanteen->campus_location }}</span>
                            </div>
                        </div>

                        <!-- Toggle Open/Closed -->
                        <form action="{{ route('seller.toggle-status') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-1.5 px-2.5 rounded-xl text-[11px] font-bold flex items-center justify-between transition-colors {{ $myCanteen->is_open ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-700/60 hover:bg-emerald-900' : 'bg-stone-900 text-stone-400 border border-stone-700 hover:bg-stone-800' }}">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full {{ $myCanteen->is_open ? 'bg-emerald-400 animate-pulse' : 'bg-stone-500' }}"></span>
                                    <span>{{ $myCanteen->is_open ? 'Stan Buka' : 'Stan Tutup' }}</span>
                                </span>
                                <span class="text-[10px] underline">Ubah</span>
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Nav Menu -->
                <nav class="p-3 space-y-1 text-sm font-semibold">
                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.dashboard*') ? 'bg-amber-600 text-white shadow-md font-bold' : 'text-amber-100 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('seller.orders.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.orders*') ? 'bg-amber-600 text-white shadow-md font-bold' : 'text-amber-100 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span>Antrean Pesanan</span>
                    </a>

                    <a href="{{ route('seller.menus.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.menus*') ? 'bg-amber-600 text-white shadow-md font-bold' : 'text-amber-100 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Kelola Menu & Stok</span>
                    </a>

                    <a href="{{ route('seller.reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.reports*') ? 'bg-amber-600 text-white shadow-md font-bold' : 'text-amber-100 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span>Laporan Omzet</span>
                    </a>

                    @if($myCanteen)
                        <div class="pt-4 pb-1">
                            <span class="text-[10px] font-bold text-amber-400/60 uppercase tracking-wider px-3">Tinjauan</span>
                        </div>
                        <a href="{{ route('customer.canteens.show', $myCanteen) }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-amber-200 hover:bg-white/5 hover:text-white transition-colors">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            <span>Lihat Tampilan Stan ↗</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Profile & Logout -->
            <div class="p-4 border-t border-amber-900/50 shrink-0">
                <div class="flex items-center gap-3 px-2 py-2">
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-9 h-9 rounded-full object-cover border border-amber-400/50">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-amber-300 truncate">Penjual Kantin</p>
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
            <header class="bg-white border-b border-amber-200/50 px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-black text-stone-900">@yield('page_title', 'Dashboard Penjual')</h1>
                    <p class="text-xs text-stone-500">@yield('page_subtitle', 'Pantau antrean pesanan dan aktivitas kantin hari ini.')</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-stone-600 bg-stone-100 px-3 py-1.5 rounded-full border border-stone-200">
                        📅 {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </header>

            <!-- Main Body -->
            <main class="flex-1 p-6 max-w-7xl w-full mx-auto space-y-5">
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
