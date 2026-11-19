@extends('layouts.admin')

@section('title', 'Kelola Penjual')
@section('page_title', 'Manajemen Akun Penjual')
@section('page_subtitle', 'Kelola akun seller mitra kantin kampus Universitas Pamulang')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Search -->
        <form action="{{ route('admin.sellers.index') }}" method="GET" class="flex items-center gap-2 flex-1 max-w-md">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, atau email..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-stone-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-sm">
                <svg class="w-4 h-4 text-stone-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-stone-800 hover:bg-stone-900 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.sellers.index') }}" class="px-3 py-2.5 text-stone-500 hover:text-stone-800 text-sm font-medium">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.sellers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold shadow-md shadow-amber-600/20 transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span>Tambah Penjual Baru</span>
        </a>
    </div>

    <!-- Table of Sellers -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-stone-50/80 border-b border-stone-200 text-stone-600 text-xs uppercase tracking-wider font-bold">
                        <th class="p-4">Nama Penjual</th>
                        <th class="p-4">Username & Email</th>
                        <th class="p-4">Kantin yang Dikelola</th>
                        <th class="p-4">No. Telepon / WA</th>
                        <th class="p-4 text-center">Status Akun</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($sellers as $seller)
                        <tr class="hover:bg-amber-50/30 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $seller->avatar_url }}" alt="" class="w-10 h-10 rounded-full object-cover border border-amber-200">
                                    <span class="font-bold text-stone-900">{{ $seller->name }}</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="font-mono text-xs font-bold text-stone-800">{{ $seller->username }}</div>
                                <div class="text-xs text-stone-500">{{ $seller->email }}</div>
                            </td>
                            <td class="p-4">
                                @if($seller->canteen)
                                    <a href="{{ route('admin.canteens.show', $seller->canteen) }}" class="inline-flex items-center gap-1.5 font-bold text-amber-800 hover:underline">
                                        🏪 {{ $seller->canteen->name }}
                                    </a>
                                    <span class="block text-xs text-stone-500">{{ $seller->canteen->campus_location }}</span>
                                @else
                                    <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">Belum terhubung ke kantin</span>
                                @endif
                            </td>
                            <td class="p-4 text-stone-600">
                                {{ $seller->phone ?? '-' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($seller->is_active)
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <form action="{{ route('admin.sellers.toggle-status', $seller) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-stone-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors" title="{{ $seller->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.sellers.edit', $seller) }}" class="p-1.5 text-stone-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form action="{{ route('admin.sellers.destroy', $seller) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun penjual ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Akun">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-500">Belum ada akun penjual yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sellers->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $sellers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
