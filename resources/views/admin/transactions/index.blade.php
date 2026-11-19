@extends('layouts.admin')

@section('title', 'Monitoring Transaksi')
@section('page_title', 'Monitoring Transaksi Multi-Kantin')
@section('page_subtitle', 'Pantau arus pesanan, pembayaran, dan omzet seluruh kantin Universitas Pamulang secara transparan')

@section('content')
<div class="space-y-6">
    <!-- Filter Bar -->
    <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm">
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-stone-500 uppercase mb-1">Cari Kode / Pelanggan</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Contoh: A-001, Budi..."
                       class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition">
            </div>

            <!-- Filter Kantin -->
            <div>
                <label class="block text-[11px] font-bold text-stone-500 uppercase mb-1">Stan Kantin</label>
                <select name="canteen_id" class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-sm bg-white focus:border-amber-500 outline-none">
                    <option value="">Semua Kantin</option>
                    @foreach($canteens as $cant)
                        <option value="{{ $cant->id }}" {{ request('canteen_id') == $cant->id ? 'selected' : '' }}>
                            {{ $cant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-bold text-stone-500 uppercase mb-1">Status Pesanan</label>
                <select name="status" class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-sm bg-white focus:border-amber-500 outline-none">
                    <option value="">Semua Status</option>
                    <option value="Menunggu konfirmasi" {{ request('status') === 'Menunggu konfirmasi' ? 'selected' : '' }}>Menunggu konfirmasi</option>
                    <option value="Diterima" {{ request('status') === 'Diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="Disiapkan" {{ request('status') === 'Disiapkan' ? 'selected' : '' }}>Disiapkan</option>
                    <option value="Siap diambil" {{ request('status') === 'Siap diambil' ? 'selected' : '' }}>Siap diambil</option>
                    <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Dibatalkan" {{ request('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Filter Metode & Submit Buttons -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-stone-500 uppercase mb-1">Metode</label>
                    <select name="payment_method" class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-sm bg-white focus:border-amber-500 outline-none">
                        <option value="">Semua</option>
                        <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai (Kasir)</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl transition shadow-sm">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'canteen_id', 'status', 'payment_method', 'date']))
                    <a href="{{ route('admin.transactions.index') }}" class="p-2 bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-xl transition text-center" title="Reset filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-stone-500 font-semibold text-xs uppercase tracking-wider">
                        <th class="py-4 px-6">Kode Pesanan</th>
                        <th class="py-4 px-6">Kantin</th>
                        <th class="py-4 px-6">Pelanggan</th>
                        <th class="py-4 px-6">Waktu</th>
                        <th class="py-4 px-6">Nominal</th>
                        <th class="py-4 px-6">Metode & Bayar</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-amber-50/40 transition">
                        <td class="py-4 px-6 font-mono font-bold text-amber-900">
                            {{ $order->order_code }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-bold text-stone-800 block">{{ $order->canteen->name ?? 'Kantin Terhapus' }}</span>
                            <span class="text-xs text-stone-400 block">{{ $order->canteen->campus_location ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-bold text-stone-800 block">{{ $order->customer_name }}</span>
                            <span class="text-xs text-stone-400 block">
                                {{ $order->customer ? ucfirst($order->customer->user_type ?? 'User') : 'Tamu' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-xs text-stone-600">
                            <div>{{ $order->created_at->format('d/m/Y') }}</div>
                            <div class="text-stone-400">{{ $order->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="py-4 px-6 font-bold text-stone-900">
                            Rp{{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="text-xs font-semibold text-stone-700 block">{{ strtoupper($order->payment_method) }}</span>
                            @if($order->payment_status === 'Lunas')
                                <span class="inline-flex items-center text-[11px] font-bold text-emerald-700">● Lunas</span>
                            @else
                                <span class="inline-flex items-center text-[11px] font-bold text-amber-600">○ Belum Bayar</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @php
                                $badgeClasses = match($order->status) {
                                    'Selesai' => 'bg-emerald-100 text-emerald-800',
                                    'Dibatalkan' => 'bg-rose-100 text-rose-800',
                                    'Siap diambil' => 'bg-blue-100 text-blue-800',
                                    'Disiapkan' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-stone-100 text-stone-800',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeClasses }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('admin.transactions.show', $order) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-stone-100 hover:bg-amber-100 text-stone-700 hover:text-amber-900 transition">
                                Detail
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-stone-400">
                            Belum ada riwayat transaksi yang sesuai kriteria pencarian.
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
