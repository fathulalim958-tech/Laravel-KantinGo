@extends('layouts.admin')

@section('title', 'Kelola Pelanggan')
@section('page_title', 'Manajemen Akun Civitas Kampus')
@section('page_subtitle', 'Kelola akun mahasiswa, dosen, dan staf universitas pemesan makanan di KantinGO')

@section('content')
<div class="space-y-6">
    <!-- Filter and Search Bar -->
    <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full">
            <!-- Search -->
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, NIM, email..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition">
                <svg class="w-5 h-5 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Role Filter -->
            <select name="user_type" onchange="this.form.submit()"
                    class="px-4 py-2.5 rounded-xl border border-stone-300 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition">
                <option value="">Semua Civitas</option>
                <option value="mahasiswa" {{ request('user_type') === 'mahasiswa' ? 'selected' : '' }}>🎓 Mahasiswa</option>
                <option value="dosen" {{ request('user_type') === 'dosen' ? 'selected' : '' }}>👨‍🏫 Dosen</option>
                <option value="staf" {{ request('user_type') === 'staf' ? 'selected' : '' }}>💼 Staf Kampus</option>
            </select>

            <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition shadow-sm">
                Cari
            </button>

            @if(request()->hasAny(['search', 'user_type']))
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-600 text-sm font-semibold rounded-xl transition text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-stone-500 font-semibold text-xs uppercase tracking-wider">
                        <th class="py-4 px-6">Pengguna</th>
                        <th class="py-4 px-6">Jenis Civitas</th>
                        <th class="py-4 px-6">NIM / NIP</th>
                        <th class="py-4 px-6">No. Telepon</th>
                        <th class="py-4 px-6">Status Akun</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($customers as $c)
                    <tr class="hover:bg-amber-50/40 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <img src="{{ $c->avatar_url }}" alt="{{ $c->name }}" class="w-10 h-10 rounded-full object-cover border border-amber-200 bg-amber-50">
                                <div>
                                    <strong class="font-bold text-stone-900 block">{{ $c->name }}</strong>
                                    <span class="text-xs text-stone-500 block">{{ '@' . $c->username }} · {{ $c->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            @if($c->user_type === 'mahasiswa')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    🎓 Mahasiswa
                                </span>
                            @elseif($c->user_type === 'dosen')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    👨‍🏫 Dosen
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    💼 Staf Kampus
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-mono text-xs text-stone-700">
                            {{ $c->identifier_number ?? '-' }}
                        </td>
                        <td class="py-4 px-6 text-stone-600">
                            {{ $c->phone ?? '-' }}
                        </td>
                        <td class="py-4 px-6">
                            @if($c->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            <a href="{{ route('admin.customers.show', $c) }}" class="inline-block p-1.5 rounded-lg text-stone-500 hover:text-amber-700 hover:bg-amber-100 transition" title="Detail Profil & Pesanan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            <a href="{{ route('admin.customers.edit', $c) }}" class="inline-block p-1.5 rounded-lg text-stone-500 hover:text-blue-700 hover:bg-blue-100 transition" title="Edit Akun">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.customers.toggle-status', $c) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status akun ini?')">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-lg text-stone-500 {{ $c->is_active ? 'hover:text-rose-700 hover:bg-rose-100' : 'hover:text-emerald-700 hover:bg-emerald-100' }} transition" title="{{ $c->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                    @if($c->is_active)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-stone-400">
                            Tidak ada pelanggan yang cocok dengan pencarian / filter ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
        <div class="p-4 border-t border-stone-200">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
