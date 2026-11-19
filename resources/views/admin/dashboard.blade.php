@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Pusat Monitoring Platform KantinGO')
@section('page_subtitle', 'Statistik terpusat dan aktivitas seluruh stan kantin Universitas Pamulang')

@section('content')
<div class="space-y-6">
    <!-- Platform Top Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card Omzet -->
        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider block">Total Omzet Platform</span>
                <strong class="text-2xl font-black text-amber-900 block mt-1">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</strong>
            </div>
            <span class="text-[11px] text-stone-500 mt-2 block">Hari ini: <strong>Rp{{ number_format($todayRevenue, 0, ',', '.') }}</strong></span>
        </div>

        <!-- Card Pesanan -->
        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Total Transaksi</span>
                <strong class="text-3xl font-black text-stone-900 block mt-1">{{ $totalOrders }}</strong>
            </div>
            <span class="text-[11px] text-stone-500 mt-2 block">Hari ini: <strong>{{ $todayOrdersCount }} pesanan</strong></span>
        </div>

        <!-- Card Kantin -->
        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">Stan Kantin Aktif</span>
                <strong class="text-3xl font-black text-emerald-800 block mt-1">{{ $openCanteens }} <span class="text-base text-stone-400 font-bold">/ {{ $totalCanteens }}</span></strong>
            </div>
            <span class="text-[11px] text-stone-500 mt-2 block">{{ $totalMenus }} menu aktif terdaftar</span>
        </div>

        <!-- Card Pengguna -->
        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider block">Civitas Terdaftar</span>
                <strong class="text-3xl font-black text-blue-900 block mt-1">{{ $totalUsers }}</strong>
            </div>
            <div class="flex items-center gap-2 text-[10px] text-stone-500 font-semibold mt-2">
                <span>🎓 {{ $totalMahasiswa }} Mhs</span>
                <span>·</span>
                <span>👨‍🏫 {{ $totalDosen }} Dosen</span>
                <span>·</span>
                <span>🏪 {{ $totalSellers }} Seller</span>
            </div>
        </div>
    </div>

    <!-- Canteen Performance & Rankings -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-black text-stone-900 text-base">Performa & Omzet Antar-Kantin</h3>
                <p class="text-xs text-stone-500">Peringkat akumulasi penjualan stan kantin di kampus UNPAM</p>
            </div>
            <a href="{{ route('admin.canteens.index') }}" class="text-xs font-bold text-amber-700 hover:underline">
                Kelola Seluruh Kantin →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($canteenStats as $idx => $canteen)
                <div class="p-4 rounded-2xl bg-amber-50/40 border border-amber-200/60 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $canteen->logo_url }}" alt="" class="w-12 h-12 rounded-xl object-contain bg-white p-1 border">
                        <div>
                            <span class="text-[10px] font-bold text-amber-800 uppercase">#{{ $idx + 1 }} · {{ $canteen->campus_location }}</span>
                            <h4 class="font-bold text-stone-900 text-sm">{{ $canteen->name }}</h4>
                            <span class="text-xs text-stone-500">{{ $canteen->orders_count }} pesanan lunas</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <strong class="text-sm font-black text-amber-900 block">Rp{{ number_format($canteen->revenue, 0, ',', '.') }}</strong>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $canteen->is_open ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-600' }}">
                            {{ $canteen->is_open ? 'Buka' : 'Tutup' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Two Columns: Recent Orders & Activity Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-black text-stone-900 text-base">Pesanan Terkini Lintas Kantin</h3>
                    <p class="text-xs text-stone-500">Monitoring real-time transaksi seluruh kampus</p>
                </div>
                <a href="{{ route('admin.transactions.index') }}" class="text-xs font-bold text-amber-700 hover:underline">
                    Semua Transaksi →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-stone-50 text-stone-600 uppercase font-bold border-b border-stone-100">
                            <th class="p-3">Kode</th>
                            <th class="p-3">Stan Kantin</th>
                            <th class="p-3">Pemesan</th>
                            <th class="p-3">Total</th>
                            <th class="p-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($recentOrders as $order)
                            <tr>
                                <td class="p-3 font-black text-amber-800">
                                    <a href="{{ route('admin.transactions.show', $order) }}" class="hover:underline">
                                        {{ $order->order_code }}
                                    </a>
                                </td>
                                <td class="p-3 font-semibold text-stone-800">{{ $order->canteen->name }}</td>
                                <td class="p-3 text-stone-700">{{ $order->customer_name }}</td>
                                <td class="p-3 font-bold text-stone-900">{{ $order->formatted_total }}</td>
                                <td class="p-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $order->status === 'Selesai' ? 'bg-stone-100 text-stone-600' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity Logs (1 col) -->
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-black text-stone-900 text-base">Log Audit Terkini</h3>
                    <p class="text-xs text-stone-500">Rekam jejak aktivitas platform</p>
                </div>
                <a href="{{ route('admin.activity-logs.index') }}" class="text-xs font-bold text-amber-700 hover:underline">
                    Lihat Semua →
                </a>
            </div>

            <div class="space-y-3">
                @foreach($recentLogs as $log)
                    <div class="p-3 rounded-2xl bg-stone-50 border border-stone-100 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-amber-800 text-[11px]">{{ $log->action }}</span>
                            <span class="text-[10px] text-stone-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-stone-700 text-[11px] leading-relaxed">{{ $log->description }}</p>
                        <span class="text-[10px] text-stone-400 block font-medium">Oleh: {{ $log->user->name ?? 'Sistem' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
