@extends('layouts.seller')

@section('title', 'Kelola Menu · ' . $canteen->name)
@section('page_title', 'Manajemen Menu & Stok')
@section('page_subtitle', 'Atur menu makanan, minuman, harga, dan ketersediaan stok stan kantin Anda')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Search & Filter -->
        <form action="{{ route('seller.menus.index') }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama menu..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl bg-white border border-stone-200 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500/20 shadow-sm">
                <svg class="w-3.5 h-3.5 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <select name="kategori" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-white border border-stone-200 text-xs text-stone-700 focus:outline-none focus:ring-2 focus:ring-amber-500/20 shadow-sm">
                <option value="">Semua Kategori</option>
                <option value="Makanan" {{ request('kategori') === 'Makanan' ? 'selected' : '' }}>Makanan</option>
                <option value="Minuman" {{ request('kategori') === 'Minuman' ? 'selected' : '' }}>Minuman</option>
            </select>
            <button type="submit" class="px-3 py-2 bg-stone-800 text-white rounded-xl text-xs font-bold shadow-sm">Cari</button>
            @if(request()->hasAny(['q', 'kategori']))
                <a href="{{ route('seller.menus.index') }}" class="text-xs text-stone-500 hover:underline">Reset</a>
            @endif
        </form>

        <a href="{{ route('seller.menus.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md shadow-amber-600/20 transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Menu Baru</span>
        </a>
    </div>

    <!-- Menus Table -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-stone-50 text-stone-600 text-xs uppercase font-bold border-b border-stone-100">
                        <th class="p-4">Menu</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Harga Satuan</th>
                        <th class="p-4 text-center">Stok Porsi</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($menus as $menu)
                        <tr class="hover:bg-amber-50/20 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $menu->image_url }}" alt="" class="w-12 h-12 rounded-xl object-contain bg-amber-50 p-1 border shrink-0">
                                    <div>
                                        <strong class="text-stone-900 text-sm block">{{ $menu->name }}</strong>
                                        <span class="text-[11px] text-stone-400 line-clamp-1">{{ $menu->description ?? 'Tanpa deskripsi' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-xs font-semibold text-stone-600">{{ $menu->kategori }}</td>
                            <td class="p-4 font-black text-amber-800 text-sm">{{ $menu->formatted_price }}</td>
                            <td class="p-4 text-center">
                                <span class="font-bold text-xs {{ $menu->stock <= 5 ? 'text-rose-600 bg-rose-50 px-2 py-0.5 rounded-lg' : 'text-stone-800' }}">
                                    {{ $menu->stock }} porsi
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if($menu->stock > 0 && $menu->is_active)
                                    <span class="px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200">Tersedia</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold text-rose-700 bg-rose-50 rounded-full border border-rose-200">Habis</span>
                                @endif
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('seller.menus.edit', $menu) }}" class="p-1.5 text-stone-600 hover:text-amber-800 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Menu">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form action="{{ route('seller.menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Hapus menu {{ $menu->name }} dari katalog? Riwayat pesanan tetap aman.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Menu">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-500">Belum ada menu yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($menus->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $menus->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
