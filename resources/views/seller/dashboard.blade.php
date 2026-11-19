@extends('layouts.seller')

@section('title', 'Dashboard Penjual · ' . $canteen->name)
@section('page_title', 'Dashboard Penjual')
@section('page_subtitle', 'Pantau aktivitas antrean pesanan dan omzet stan kantin Anda hari ini.')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-amber-700 via-amber-800 to-amber-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-amber-300 uppercase tracking-wider block">Stan: {{ $canteen->name }}</span>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mt-0.5">
                Selamat datang, {{ auth()->user()->name }}!
            </h2>
            <p class="text-xs sm:text-sm text-amber-100/90 mt-1">
                📍 {{ $canteen->campus_location }} · Status saat ini: <strong>{{ $canteen->is_open ? 'Buka (Menerima Pesanan)' : 'Tutup Sementara' }}</strong>
            </p>
        </div>

        <form action="{{ route('seller.toggle-status') }}" method="POST">
            @csrf
            <button type="submit" class="px-5 py-2.5 rounded-2xl text-xs font-bold shadow-md transition-all {{ $canteen->is_open ? 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-200 border border-rose-400/30' : 'bg-emerald-500 hover:bg-emerald-600 text-white' }}">
                {{ $canteen->is_open ? 'Tutup Stan Sementara' : 'Buka Stan Sekarang' }}
            </button>
        </form>
    </div>

    <!-- 4 Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Pesanan Hari Ini</span>
            <strong class="text-3xl font-black text-stone-900 block mt-1">{{ $totalToday }}</strong>
            <span class="text-[11px] text-stone-400 mt-1 block">Seluruh antrean hari ini</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block">Sedang Disiapkan</span>
            <strong class="text-3xl font-black text-blue-700 block mt-1">{{ $preparingCount }}</strong>
            <span class="text-[11px] text-stone-400 mt-1 block">Perlu segera dimasak/dikemas</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Siap Diambil</span>
            <strong class="text-3xl font-black text-emerald-700 block mt-1">{{ $readyCount }}</strong>
            <span class="text-[11px] text-stone-400 mt-1 block">Menunggu kedatangan siswa/dosen</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold text-amber-700 uppercase tracking-wider block">Penerimaan Tunai</span>
            <strong class="text-2xl font-black text-amber-800 block mt-1">Rp{{ number_format($cashRevenue, 0, ',', '.') }}</strong>
            <span class="text-[11px] text-stone-400 mt-1 block">Uang tunai kasir diterima</span>
        </div>
    </div>

    <!-- QRIS note -->
    <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-3.5 px-4 text-xs text-amber-900 flex items-center justify-between">
        <span>📱 Penerimaan QRIS simulasi hari ini: <strong>Rp{{ number_format($qrisRevenue, 0, ',', '.') }}</strong> (Khusus transaksi terkonfirmasi lunas).</span>
        <a href="{{ route('seller.reports.index') }}" class="font-bold underline text-amber-800">Lihat Laporan Lengkap →</a>
    </div>

    <!-- Active Orders Queue Table -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
            <div>
                <h3 class="font-black text-stone-900 text-base">Antrean Pesanan Masuk</h3>
                <p class="text-xs text-stone-500">Daftar pesanan aktif hari ini yang perlu diproses</p>
            </div>
            <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-amber-700 hover:underline">
                Lihat Seluruh Riwayat Pesanan →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-stone-50/80 text-stone-600 text-xs uppercase font-bold border-b border-stone-100">
                        <th class="p-4">Kode & Waktu</th>
                        <th class="p-4">Pemesan</th>
                        <th class="p-4">Rincian Menu</th>
                        <th class="p-4">Total & Bayar</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($queueOrders as $order)
                        <tr class="hover:bg-amber-50/20 transition-colors">
                            <td class="p-4">
                                <a href="{{ route('seller.orders.show', $order) }}" class="font-black text-amber-800 hover:underline text-sm block">
                                    {{ $order->order_code }}
                                </a>
                                <span class="text-[11px] text-stone-400 font-mono">{{ $order->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="p-4">
                                <strong class="text-stone-900 text-xs block">{{ $order->customer_name }}</strong>
                                <span class="text-[11px] text-stone-500">{{ $order->customer_type }}</span>
                            </td>
                            <td class="p-4 max-w-xs">
                                <p class="text-xs text-stone-700 line-clamp-1 font-medium">{{ $order->items_summary }}</p>
                            </td>
                            <td class="p-4">
                                <strong class="text-stone-900 text-xs block">{{ $order->formatted_total }}</strong>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $order->payment_status === 'Lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $order->payment_method }} · {{ $order->payment_status }}
                                </span>
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
                            <td class="p-4 text-right">
                                @if($order->status === 'Diterima')
                                    <form action="{{ route('seller.orders.status', $order) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="expected" value="Diterima">
                                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition-all">
                                            Siapkan
                                        </button>
                                    </form>
                                @elseif($order->status === 'Disiapkan')
                                    <form action="{{ route('seller.orders.status', $order) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="expected" value="Disiapkan">
                                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all">
                                            Siap Diambil
                                        </button>
                                    </form>
                                @elseif($order->status === 'Siap diambil')
                                    <form action="{{ route('seller.orders.status', $order) }}" method="POST" onsubmit="return confirm('{{ $order->payment_method === 'Tunai' && $order->payment_status !== 'Lunas' ? 'Pastikan uang tunai sebesar ' . $order->formatted_total . ' sudah diterima. Selesaikan pesanan?' : 'Selesaikan pesanan ' . $order->order_code . '?' }}')">
                                        @csrf
                                        <input type="hidden" name="expected" value="Siap diambil">
                                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm transition-all"
                                                {{ ($order->payment_method === 'QRIS' && $order->payment_status !== 'Lunas') ? 'disabled title="Menunggu simulasi pembayaran QRIS"' : '' }}>
                                            {{ $order->payment_status === 'Lunas' ? 'Selesaikan' : 'Bayar & Selesai' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-stone-400 font-semibold">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-500">
                                Tidak ada antrean pesanan baru saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
