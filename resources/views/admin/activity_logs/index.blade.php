@extends('layouts.admin')

@section('title', 'Log Aktivitas Sistem')
@section('page_title', 'Audit Trail & Log Aktivitas')
@section('page_subtitle', 'Rekam jejak seluruh operasi krusial pengguna, perubahan status stan, dan transaksi sistem')

@section('content')
<div class="space-y-6">
    <!-- Filter and Search Bar -->
    <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full">
            <!-- Search -->
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari deskripsi, aksi, atau IP address..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition">
                <svg class="w-5 h-5 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Action Type Filter -->
            <select name="action" onchange="this.form.submit()"
                    class="px-4 py-2.5 rounded-xl border border-stone-300 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition">
                <option value="">Semua Jenis Aksi</option>
                @foreach($actionTypes as $type)
                    <option value="{{ $type }}" {{ request('action') === $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition shadow-sm">
                Filter
            </button>

            @if(request()->hasAny(['q', 'action']))
                <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-600 text-sm font-semibold rounded-xl transition text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-stone-500 font-semibold text-xs uppercase tracking-wider">
                        <th class="py-4 px-6">Waktu Kejadian</th>
                        <th class="py-4 px-6">Pengguna (Aktor)</th>
                        <th class="py-4 px-6">Aksi</th>
                        <th class="py-4 px-6">Deskripsi Aktivitas</th>
                        <th class="py-4 px-6">IP Address & Browser</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 font-sans">
                    @forelse($logs as $log)
                    <tr class="hover:bg-amber-50/30 transition">
                        <td class="py-4 px-6 text-xs text-stone-600 whitespace-nowrap">
                            <strong class="font-bold text-stone-900 block">{{ $log->created_at->format('d/m/Y H:i:s') }}</strong>
                            <span class="text-stone-400 block">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            @if($log->user)
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $log->user->avatar_url }}" alt="{{ $log->user->name }}" class="w-7 h-7 rounded-full object-cover border border-amber-200">
                                    <div>
                                        <strong class="font-bold text-stone-900 text-xs block">{{ $log->user->name }}</strong>
                                        <span class="text-[10px] text-stone-500 uppercase font-semibold">
                                            @if($log->user->isAdmin())
                                                🛡️ Admin
                                            @elseif($log->user->isSeller())
                                                🏪 Penjual
                                            @else
                                                🎓 {{ ucfirst($log->user->user_type ?? 'Customer') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-stone-400 font-medium">Sistem Otomatis / Tamu</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-mono font-bold bg-stone-100 text-stone-800 border border-stone-200">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-stone-800 text-xs leading-relaxed max-w-md">
                            {{ $log->description }}
                        </td>
                        <td class="py-4 px-6 text-[11px] font-mono text-stone-500 whitespace-nowrap">
                            <div>{{ $log->ip_address ?? '127.0.0.1' }}</div>
                            <div class="text-[10px] text-stone-400 truncate max-w-[180px]" title="{{ $log->user_agent }}">
                                {{ $log->user_agent ? Str::limit($log->user_agent, 25) : '-' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-stone-400">
                            Belum ada riwayat aktivitas yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-stone-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
