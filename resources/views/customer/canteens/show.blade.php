@extends('layouts.customer')

@section('title', $canteen->name . ' · Menu & Pemesanan')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Back button -->
    <div>
        <a href="{{ route('customer.canteens') }}" class="inline-flex items-center gap-2 text-xs font-bold text-stone-600 hover:text-amber-800 bg-white/80 px-3 py-1.5 rounded-xl border border-stone-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Kantin</span>
        </a>
    </div>

    <!-- Canteen Header Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-start sm:items-center gap-5">
            <img src="{{ $canteen->logo_url }}" alt="{{ $canteen->name }}"
                 class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl object-contain bg-amber-50 p-2.5 border border-amber-200 shadow-sm shrink-0">
            <div>
                <div class="flex flex-wrap items-center gap-2.5 mb-1.5">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200/80">
                        Stan Terverifikasi
                    </span>
                    @if($canteen->is_open)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Buka Sekarang
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-stone-100 text-stone-500 border border-stone-200">
                            <span class="w-2 h-2 rounded-full bg-stone-400"></span> Tutup Sementara
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight">
                    {{ $canteen->name }}
                </h1>
                <p class="text-xs sm:text-sm text-amber-800 font-semibold mt-1">
                    📍 {{ $canteen->campus_location }} · 🕒 {{ $canteen->opening_hours ?? '07:30 - 17:00 WIB' }}
                </p>
                <p class="text-xs text-stone-500 mt-1 max-w-xl">
                    {{ $canteen->description ?? 'Menyajikan aneka makanan dan minuman lezat dan higienis untuk civitas UNPAM.' }}
                </p>
            </div>
        </div>

        @if($cartCanteenId && $cartCanteenId != $canteen->id)
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs max-w-xs shrink-0">
                <span class="font-bold block mb-1">⚠️ Catatan Keranjang:</span>
                Keranjang Anda saat ini berisi menu dari kantin lain. Menambahkan menu dari kantin ini akan meminta konfirmasi pemindahan keranjang.
            </div>
        @endif
    </div>

    <!-- Category Filter Tabs & Search -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
        <!-- Categories -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
            <a href="{{ route('customer.canteens.show', ['canteen' => $canteen, 'q' => request('q')]) }}"
               class="px-4 py-2 rounded-xl whitespace-nowrap transition-all {{ !request('kategori') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                Semua Menu ({{ $canteen->menus()->where('is_active', true)->count() }})
            </a>
            <a href="{{ route('customer.canteens.show', ['canteen' => $canteen, 'kategori' => 'Makanan', 'q' => request('q')]) }}"
               class="px-4 py-2 rounded-xl whitespace-nowrap transition-all {{ request('kategori') === 'Makanan' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                🍛 Makanan
            </a>
            <a href="{{ route('customer.canteens.show', ['canteen' => $canteen, 'kategori' => 'Minuman', 'q' => request('q')]) }}"
               class="px-4 py-2 rounded-xl whitespace-nowrap transition-all {{ request('kategori') === 'Minuman' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                🥤 Minuman
            </a>
        </div>

        <!-- Search in this canteen -->
        <form action="{{ route('customer.canteens.show', $canteen) }}" method="GET" class="flex items-center gap-2 max-w-xs w-full">
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari menu di kantin ini..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl bg-white border border-stone-200 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-sm">
                <svg class="w-3.5 h-3.5 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="px-3 py-2 bg-stone-800 text-white rounded-xl text-xs font-bold shadow-sm">Cari</button>
        </form>
    </div>

    <!-- Menus Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @forelse($menus as $menu)
            <div class="bg-white rounded-3xl border border-stone-200/80 shadow-sm hover:shadow-lg transition-all p-4 flex flex-col justify-between group">
                <div>
                    <!-- Menu Image -->
                    <div class="w-full h-36 rounded-2xl bg-amber-50 flex items-center justify-center p-3 relative overflow-hidden border border-amber-100/60 mb-3.5">
                        <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform">
                        <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase bg-white/90 text-amber-900 shadow-sm border border-stone-200/50">
                            {{ $menu->kategori }}
                        </span>
                        @if($menu->stock <= 0)
                            <div class="absolute inset-0 bg-stone-900/60 backdrop-blur-[2px] flex items-center justify-center">
                                <span class="px-3 py-1 rounded-full bg-rose-600 text-white text-xs font-bold shadow-md">Stok Habis</span>
                            </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <h3 class="font-bold text-stone-900 text-sm group-hover:text-amber-700 transition-colors line-clamp-1">
                        {{ $menu->name }}
                    </h3>
                    <p class="text-[11px] text-stone-500 line-clamp-2 mt-1 min-h-[32px]">
                        {{ $menu->description ?? 'Menu istimewa pilihan kantin kampus UNPAM.' }}
                    </p>
                </div>

                <div class="pt-3 border-t border-stone-100 mt-3 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] text-stone-400 font-medium">Stok: {{ $menu->stock }} porsi</span>
                        <strong class="text-base font-black text-amber-800">{{ $menu->formatted_price }}</strong>
                    </div>

                    <!-- Add to Cart Form -->
                    <form action="{{ route('customer.cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        <button type="submit" {{ ($menu->stock <= 0 || !$canteen->is_open) ? 'disabled' : '' }}
                                class="px-3 py-1.5 rounded-xl {{ ($menu->stock > 0 && $canteen->is_open) ? 'bg-amber-600 hover:bg-amber-700 text-white shadow-md shadow-amber-600/20 active:scale-95' : 'bg-stone-100 text-stone-400 cursor-not-allowed' }} text-xs font-bold transition-all flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>{{ ($menu->stock > 0 && $canteen->is_open) ? '+ Pesan' : 'Habis' }}</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-10 text-center border border-stone-200">
                <p class="text-sm font-semibold text-stone-600">Tidak ada menu yang sesuai kriteria pencarian di kantin ini.</p>
                <a href="{{ route('customer.canteens.show', $canteen) }}" class="inline-block mt-2 text-xs font-bold text-amber-700 hover:underline">Tampilkan Semua Menu</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
