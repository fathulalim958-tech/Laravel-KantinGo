@extends('layouts.customer')

@section('title', 'Katalog Kantin UNPAM')

@section('content')
<div class="space-y-8">
    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-700 via-amber-800 to-amber-950 text-white p-6 sm:p-10 shadow-xl shadow-amber-900/10">
        <div class="relative z-10 max-w-2xl space-y-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30 text-xs font-bold uppercase tracking-wider">
                🎓 Universitas Pamulang Food Court
            </span>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
                Mau makan apa hari ini di kampus?
            </h1>
            <p class="text-amber-100/90 text-sm sm:text-base leading-relaxed">
                Pesan makanan dan minuman favoritmu dari stan kantin kampus. Pesanan disiapkan lebih awal, ambil langsung saat jam istirahat tanpa perlu berdesakan.
            </p>
        </div>
        <!-- Decorative Background Circle -->
        <div class="absolute -right-16 -bottom-16 w-80 h-80 rounded-full bg-amber-600/20 blur-3xl pointer-events-none"></div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="space-y-4">
        <!-- Campus Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs sm:text-sm font-bold">
            <a href="{{ route('customer.canteens') }}" class="px-4 py-2 rounded-2xl whitespace-nowrap transition-all {{ !request('campus') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                🏫 Semua Kampus
            </a>
            <a href="{{ route('customer.canteens', ['campus' => 'Viktor', 'q' => request('q')]) }}" class="px-4 py-2 rounded-2xl whitespace-nowrap transition-all {{ request('campus') === 'Viktor' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                🏢 Kampus Viktor
            </a>
            <a href="{{ route('customer.canteens', ['campus' => 'Witana', 'q' => request('q')]) }}" class="px-4 py-2 rounded-2xl whitespace-nowrap transition-all {{ request('campus') === 'Witana' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                🏛️ Kampus Witana Harja
            </a>
            <a href="{{ route('customer.canteens', ['campus' => 'Pusat', 'q' => request('q')]) }}" class="px-4 py-2 rounded-2xl whitespace-nowrap transition-all {{ request('campus') === 'Pusat' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                🏬 Kampus Pusat Pamulang
            </a>
        </div>

        <!-- Search Input -->
        <form action="{{ route('customer.canteens') }}" method="GET" class="flex items-center gap-2">
            @if(request('campus'))
                <input type="hidden" name="campus" value="{{ request('campus') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari stan kantin atau menu makanan (cth: nasi ayam, soto, mie)..."
                       class="w-full pl-10 pr-4 py-3 rounded-2xl bg-white border border-stone-200 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 text-sm shadow-sm transition-all">
                <svg class="w-5 h-5 text-stone-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="px-5 py-3 rounded-2xl bg-stone-800 hover:bg-stone-900 text-white font-bold text-sm shadow-sm transition-colors">
                Cari
            </button>
            @if(request('q'))
                <a href="{{ route('customer.canteens', ['campus' => request('campus')]) }}" class="px-3 py-3 text-stone-500 hover:text-stone-800 text-sm font-semibold">Reset</a>
            @endif
        </form>
    </div>

    <!-- Canteens Grid -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-black text-stone-900">Daftar Stan Kantin Kampus</h2>
            <span class="text-xs text-stone-500">{{ $openCount }} stan sedang buka</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($canteens as $canteen)
                <div class="bg-white rounded-3xl border border-stone-200/80 shadow-sm hover:shadow-xl hover:shadow-amber-900/5 transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                    <div class="p-6">
                        <!-- Top status & logo -->
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <img src="{{ $canteen->logo_url }}" alt="{{ $canteen->name }}"
                                 class="w-16 h-16 rounded-2xl object-contain bg-amber-50 p-2 border border-amber-100 group-hover:scale-105 transition-transform">

                            @if($canteen->is_open)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Buka Sekarang
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-stone-100 text-stone-500 border border-stone-200">
                                    <span class="w-2 h-2 rounded-full bg-stone-400"></span> Tutup Sementara
                                </span>
                            @endif
                        </div>

                        <!-- Canteen Details -->
                        <h3 class="text-lg font-black text-stone-900 group-hover:text-amber-700 transition-colors line-clamp-1">
                            {{ $canteen->name }}
                        </h3>
                        <p class="text-xs font-medium text-amber-800 mt-1 flex items-center gap-1">
                            <span>📍 {{ $canteen->campus_location }}</span>
                        </p>
                        <p class="text-xs text-stone-500 mt-2 line-clamp-2 leading-relaxed">
                            {{ $canteen->description ?? 'Stan kantin resmi kampus Universitas Pamulang.' }}
                        </p>

                        <!-- Menu Samples Preview -->
                        <div class="mt-4 pt-4 border-t border-stone-100">
                            <div class="flex items-center justify-between text-xs text-stone-500 mb-2 font-medium">
                                <span>Menu Populer:</span>
                                <span>{{ $canteen->menus->count() }} menu siap saji</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($canteen->menus->take(3) as $sampleMenu)
                                    <span class="px-2.5 py-1 rounded-xl bg-amber-50 text-amber-900 border border-amber-200/60 text-[11px] font-semibold">
                                        {{ $sampleMenu->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-stone-400 italic">Belum ada menu terdaftar.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="p-4 bg-stone-50/60 border-t border-stone-100 flex items-center justify-between">
                        <span class="text-xs font-medium text-stone-500">🕒 {{ $canteen->opening_hours ?? '08:00 - 17:00' }}</span>
                        <a href="{{ route('customer.canteens.show', $canteen) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl {{ $canteen->is_open ? 'bg-amber-600 hover:bg-amber-700 text-white shadow-md shadow-amber-600/20' : 'bg-stone-200 text-stone-500 cursor-not-allowed' }} text-xs font-bold transition-all">
                            <span>Lihat Menu & Pesan</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-stone-200">
                    <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-3 text-2xl">
                        🔍
                    </div>
                    <h3 class="text-lg font-bold text-stone-800">Kantin tidak ditemukan</h3>
                    <p class="text-sm text-stone-500 mt-1">Coba gunakan kata kunci pencarian lain atau pilih lokasi kampus yang berbeda.</p>
                    <a href="{{ route('customer.canteens') }}" class="inline-block mt-4 text-sm font-bold text-amber-700 hover:underline">
                        Lihat Seluruh Kantin
                    </a>
                </div>
            @endforelse
        </div>

        @if($canteens->hasPages())
            <div class="mt-8">
                {{ $canteens->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
