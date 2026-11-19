@extends('layouts.seller')

@section('title', 'Laporan Penjualan · ' . $canteen->name)
@section('page_title', 'Laporan Omzet & Penjualan')
@section('page_subtitle', 'Analitik pendapatan dan menu terlaris stan kantin Anda')

@section('content')
<div class="space-y-6">
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Total Omzet Keseluruhan</span>
            <strong class="text-2xl font-black text-amber-800 block mt-1">Rp{{ number_format($allTimeRevenue, 0, ',', '.') }}</strong>
            <span class="text-[11px] text-stone-400 mt-1 block">Dari {{ $allTimeOrders }} pesanan lunas</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Omzet Bulan Ini</span>
            <strong class="text-2xl font-black text-stone-900 block mt-1">Rp{{ number_format($monthRevenue, 0, ',', '.') }}</strong>
            <span class="text-[11px] text-stone-400 mt-1 block">{{ $monthOrders }} pesanan bulan {{ date('F') }}</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Omzet Minggu Ini</span>
            <strong class="text-2xl font-black text-stone-900 block mt-1">Rp{{ number_format($weekRevenue, 0, ',', '.') }}</strong>
            <span class="text-[11px] text-stone-400 mt-1 block">{{ $weekOrders }} pesanan 7 hari terakhir</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Total Transaksi Lunas</span>
            <strong class="text-2xl font-black text-emerald-700 block mt-1">{{ $allTimeOrders }}</strong>
            <span class="text-[11px] text-stone-400 mt-1 block">Pesanan terlayani sukses</span>
        </div>
    </div>

    <!-- Payment Breakdown & Best Sellers -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Methods Comparison -->
        <div class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm space-y-4">
            <h3 class="font-black text-stone-900 text-base">Metode Penerimaan</h3>
            <div class="space-y-3">
                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80">
                    <span class="text-xs font-bold text-amber-900 block">💵 Tunai Kasir</span>
                    <strong class="text-xl font-black text-amber-800 block mt-0.5">Rp{{ number_format($cashTotal, 0, ',', '.') }}</strong>
                    <span class="text-[11px] text-stone-500 block mt-1">Uang fisik diterima langsung</span>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80">
                    <span class="text-xs font-bold text-emerald-900 block">📱 QRIS Digital (Simulasi)</span>
                    <strong class="text-xl font-black text-emerald-800 block mt-0.5">Rp{{ number_format($qrisTotal, 0, ',', '.') }}</strong>
                    <span class="text-[11px] text-stone-500 block mt-1">Pembayaran terkonfirmasi sistem</span>
                </div>
            </div>
        </div>

        <!-- Best Sellers Ranking -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden p-6 space-y-4">
            <h3 class="font-black text-stone-900 text-base">Menu Terlaris (Paling Banyak Dipesan)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-stone-50 text-stone-600 uppercase font-bold border-b border-stone-100">
                            <th class="p-3">Peringkat</th>
                            <th class="p-3">Nama Menu</th>
                            <th class="p-3 text-center">Porsi Terjual</th>
                            <th class="p-3 text-right">Total Akumulasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($bestSellers as $idx => $item)
                            <tr>
                                <td class="p-3 font-bold text-stone-400">#{{ $idx + 1 }}</td>
                                <td class="p-3 font-bold text-stone-900">{{ $item->menu_name }}</td>
                                <td class="p-3 text-center font-black text-amber-800">{{ $item->total_qty }} porsi</td>
                                <td class="p-3 text-right font-black text-stone-900">Rp{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-stone-500">Belum ada riwayat pesanan yang lunas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
