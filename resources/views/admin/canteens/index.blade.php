@extends('layouts.admin')

@section('title', 'Kelola Kantin')
@section('page_title', 'Manajemen Kantin Kampus')
@section('page_subtitle', 'Daftar seluruh stan kantin aktif di lingkungan Universitas Pamulang')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Search & Filters -->
        <form action="{{ route('admin.canteens.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1 max-w-xl">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kantin atau lokasi..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-stone-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-sm">
                <svg class="w-4 h-4 text-stone-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <select name="location" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-stone-200 text-sm text-stone-600 focus:outline-none focus:ring-2 focus:ring-amber-500/20 shadow-sm">
                <option value="">Semua Kampus</option>
                <option value="Viktor" {{ request('location') === 'Viktor' ? 'selected' : '' }}>Kampus Viktor</option>
                <option value="Witana" {{ request('location') === 'Witana' ? 'selected' : '' }}>Kampus Witana Harja</option>
                <option value="Pusat" {{ request('location') === 'Pusat' ? 'selected' : '' }}>Kampus Pusat</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-stone-800 hover:bg-stone-900 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'location']))
                <a href="{{ route('admin.canteens.index') }}" class="px-3 py-2.5 text-stone-500 hover:text-stone-800 text-sm font-medium">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.canteens.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold shadow-md shadow-amber-600/20 transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Kantin Baru</span>
        </a>
    </div>

    <!-- Table of Canteens -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-stone-50/80 border-b border-stone-200 text-stone-600 text-xs uppercase tracking-wider font-bold">
                        <th class="p-4">Kantin</th>
                        <th class="p-4">Penjual (Seller)</th>
                        <th class="p-4">Lokasi Kampus</th>
                        <th class="p-4 text-center">Menu</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($canteens as $canteen)
                        <tr class="hover:bg-amber-50/30 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $canteen->logo_url }}" alt="" class="w-12 h-12 rounded-xl object-contain bg-amber-50 p-1 border border-amber-100 shrink-0">
                                    <div>
                                        <a href="{{ route('admin.canteens.show', $canteen) }}" class="font-bold text-stone-900 hover:text-amber-700 transition-colors block">
                                            {{ $canteen->name }}
                                        </a>
                                        <span class="text-xs text-stone-500">Jam: {{ $canteen->opening_hours ?? '08:00 - 16:30' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                @if($canteen->seller)
                                    <div class="font-semibold text-stone-800">{{ $canteen->seller->name }}</div>
                                    <div class="text-xs text-stone-500">{{ $canteen->seller->email }}</div>
                                @else
                                    <span class="text-xs font-semibold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200">Belum ada seller</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-stone-700 bg-stone-100 px-2.5 py-1 rounded-lg">
                                    📍 {{ $canteen->campus_location }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="font-bold text-amber-800 bg-amber-100/60 px-2.5 py-1 rounded-full text-xs">
                                    {{ $canteen->menus->count() }} item
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if ($canteen->is_open)
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Buka
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-stone-600 bg-stone-100 px-2.5 py-1 rounded-full border border-stone-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span> Tutup
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.canteens.show', $canteen) }}" class="p-1.5 text-stone-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    <form action="{{ route('admin.canteens.toggle-status', $canteen) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-stone-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors" title="{{ $canteen->is_open ? 'Tutup Kantin' : 'Buka Kantin' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.canteens.edit', $canteen) }}" class="p-1.5 text-stone-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit Informasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form action="{{ route('admin.canteens.destroy', $canteen) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan kantin ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Kantin">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-500">
                                Belum ada kantin yang terdaftar atau sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($canteens->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $canteens->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
