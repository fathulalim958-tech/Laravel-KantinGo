@extends('layouts.seller')

@section('title', 'Manajemen Pesanan · ' . $canteen->name)
@section('page_title', 'Manajemen & Riwayat Pesanan')
@section('page_subtitle', 'Kelola seluruh riwayat pesanan yang masuk ke stan kantin Anda')

@section('content')
<div class="space-y-6">
    <!-- Search & Filter Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Status Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs font-bold">
            <a href="{{ route('seller.orders.index', ['q' => request('q')]) }}" class="px-3.5 py-2 rounded-xl whitespace-nowrap transition-all {{ !request('status') ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                Semua
            </a>
            <a href="{{ route('seller.orders.index', ['status' => 'Diterima', 'q' => request('q')]) }}" class="px-3.5 py-2 rounded-xl whitespace-nowrap transition-all {{ request('status') === 'Diterima' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                Diterima
            </a>
            <a href="{{ route('seller.orders.index', ['status' => 'Disiapkan', 'q' => request('q')]) }}" class="px-3.5 py-2 rounded-xl whitespace-nowrap transition-all {{ request('status') === 'Disiapkan' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                Disiapkan
            </a>
            <a href="{{ route('seller.orders.index', ['status' => 'Siap diambil', 'q' => request('q')]) }}" class="px-3.5 py-2 rounded-xl whitespace-nowrap transition-all {{ request('status') === 'Siap diambil' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                Siap Diambil
            </a>
            <a href="{{ route('seller.orders.index', ['status' => 'Selesai', 'q' => request('q')]) }}" class="px-3.5 py-2 rounded-xl whitespace-nowrap transition-all {{ request('status') === 'Selesai' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
                Selesai
            </a>
        </div>

        <!-- Search -->
        <form action="{{ route('seller.orders.index') }}" method="GET" class="flex items-center gap-2 max-w-xs w-full">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode atau nama..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl bg-white border border-stone-200 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500/20 shadow-sm">
                <svg class="w-3.5 h-3.5 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="px-3 py-2 bg-stone-800 text-white rounded-xl text-xs font-bold shadow-sm">Cari</button>
            @if(request('q'))
                <a href="{{ route('seller.orders.index', ['status' => request('status')]) }}" class="text-xs text-stone-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-stone-50 text-stone-600 text-xs uppercase font-bold border-b border-stone-100">
                        <th class="p-4">Kode & Tanggal</th>
                        <th class="p-4">Pemesan</th>
                        <th class="p-4">Menu Dipesan</th>
                        <th class="p-4">Total</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-amber-50/20 transition-colors">
                            <td class="p-4">
                                <a href="{{ route('seller.orders.show', $order) }}" class="font-black text-amber-800 hover:underline text-sm block">
                                    {{ $order->order_code }}
                                </a>
                                <span class="text-[11px] text-stone-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="p-4">
                                <strong class="text-stone-900 text-xs block">{{ $order->customer_name }}</strong>
                                <span class="text-[11px] text-stone-500">{{ $order->customer_type }}</span>
                            </td>
                            <td class="p-4 max-w-xs">
                                <p class="text-xs text-stone-700 font-medium truncate">{{ $order->items_summary }}</p>
                                <span class="text-[10px] text-stone-400">{{ $order->payment_method }} · {{ $order->payment_status }}</span>
                            </td>
                            <td class="p-4 font-black text-stone-900 text-sm whitespace-nowrap">
                                {{ $order->formatted_total }}
                            </td>
                            <td class="p-4 text-center">
                                @php
                                    $statusBadges = [
                                        'Diterima' => 'bg-amber-100 text-amber-900 border-amber-300',
                                        'Disiapkan' => 'bg-blue-100 text-blue-900 border-blue-300',
                                        'Siap diambil' => 'bg-emerald-100 text-emerald-900 border-emerald-300 animate-pulse',
                                        'Selesai' => 'bg-stone-100 text-stone-600 border-stone-200',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusBadges[$order->status] ?? 'bg-stone-100' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('seller.orders.show', $order) }}" class="p-1.5 rounded-lg text-stone-600 hover:text-amber-800 hover:bg-amber-50" title="Lihat Rincian">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    @if($order->status === 'Diterima')
                                        <form action="{{ route('seller.orders.status', $order) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="expected" value="Diterima">
                                            <button type="submit" class="px-3 py-1 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm">
                                                Siapkan
                                            </button>
                                        </form>
                                    @elseif($order->status === 'Disiapkan')
                                        <form action="{{ route('seller.orders.status', $order) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="expected" value="Disiapkan">
                                            <button type="submit" class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm">
                                                Siap Diambil
                                            </button>
                                        </form>
                                    @elseif($order->status === 'Siap diambil')
                                        <form action="{{ route('seller.orders.status', $order) }}" method="POST" class="inline" onsubmit="return confirm('Selesaikan pesanan {{ $order->order_code }}?')">
                                            @csrf
                                            <input type="hidden" name="expected" value="Siap diambil">
                                            <button type="submit" class="px-3 py-1 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm">
                                                {{ $order->payment_status === 'Lunas' ? 'Selesaikan' : 'Bayar & Selesai' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-stone-400 font-medium">Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-500">
                                Belum ada pesanan pada filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
