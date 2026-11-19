@extends('layouts.admin')

@section('title', 'Detail Kantin: ' . $canteen->name)
@section('page_title', $canteen->name)
@section('page_subtitle', 'Informasi performa kantin, menu aktif, dan riwayat pesanan')

@section('content')
<div class="space-y-6">
    <!-- Canteen Profile Card -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <img src="{{ $canteen->logo_url }}" alt="{{ $canteen->name }}" class="w-20 h-20 rounded-2xl object-contain bg-amber-50 p-2 border border-amber-200 shadow-sm">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-black text-stone-900">{{ $canteen->name }}</h2>
                    @if ($canteen->is_open)
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Buka
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-stone-600 bg-stone-100 px-2.5 py-1 rounded-full border border-stone-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span> Tutup
                        </span>
                    @endif
                </div>
                <p class="text-sm text-stone-600 mt-1">📍 {{ $canteen->campus_location }} · 🕒 {{ $canteen->opening_hours }}</p>
                <p class="text-xs text-stone-500 mt-0.5">Penjual: <strong>{{ $canteen->seller->name ?? 'Belum ada' }}</strong> ({{ $canteen->seller->email ?? '-' }})</p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-stretch md:self-auto justify-end">
            <a href="{{ route('admin.canteens.edit', $canteen) }}" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-xl text-xs font-bold transition-colors">
                Edit Informasi
            </a>
            <form action="{{ route('admin.canteens.toggle-status', $canteen) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 {{ $canteen->is_open ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }} rounded-xl text-xs font-bold transition-colors">
                    {{ $canteen->is_open ? 'Tutup Kantin Sementara' : 'Buka Kantin Sekarang' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-stone-500">Total Omzet Terbayar</span>
            <p class="text-2xl font-black text-amber-700 mt-1">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-stone-500">Total Pesanan Diterima</span>
            <p class="text-2xl font-black text-stone-900 mt-1">{{ $totalOrders }} pesanan</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-stone-500">Jumlah Menu Aktif</span>
            <p class="text-2xl font-black text-stone-900 mt-1">{{ $canteen->menus->count() }} menu</p>
        </div>
    </div>

    <!-- Menu List of Canteen -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
            <h3 class="font-bold text-stone-900">Katalog Menu Kantin Ini</h3>
            <span class="text-xs text-stone-500">{{ $canteen->menus->count() }} menu terdaftar</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-stone-50 text-stone-600 text-xs uppercase font-bold">
                        <th class="p-4">Menu</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4 text-center">Stok Porsi</th>
                        <th class="p-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($canteen->menus as $menu)
                        <tr>
                            <td class="p-4 flex items-center gap-3">
                                <img src="{{ $menu->image_url }}" alt="" class="w-10 h-10 rounded-xl object-contain bg-amber-50 p-1 border">
                                <span class="font-semibold text-stone-900">{{ $menu->name }}</span>
                            </td>
                            <td class="p-4 text-stone-600">{{ $menu->kategori }}</td>
                            <td class="p-4 font-bold text-amber-800">{{ $menu->formatted_price }}</td>
                            <td class="p-4 text-center font-bold {{ $menu->stock <= 5 ? 'text-rose-600' : 'text-stone-800' }}">
                                {{ $menu->stock }}
                            </td>
                            <td class="p-4 text-center">
                                @if($menu->stock > 0 && $menu->is_active)
                                    <span class="px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200">Tersedia</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold text-rose-700 bg-rose-50 rounded-full border border-rose-200">Habis</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-stone-500">Penjual belum menambahkan menu makanan atau minuman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
