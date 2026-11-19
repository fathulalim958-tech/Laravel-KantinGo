@extends('layouts.admin')

@section('title', 'Transaksi #' . $order->order_code)
@section('page_title', 'Rincian Transaksi #' . $order->order_code)
@section('page_subtitle', 'Audit detail pesanan, snapshot harga menu, status bayar, dan alokasi kantin')

@section('content')
<div class="space-y-6">
    <!-- Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-600 hover:text-stone-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Monitoring Transaksi
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('orders.track', $order->access_token) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-amber-300 bg-amber-50 text-amber-900 hover:bg-amber-100 text-xs font-bold transition">
                <span>Buka Tracking Publik ↗</span>
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items & Calculation (Col 1 & 2) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Items Table Card -->
            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-stone-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-stone-900">Rincian Menu Dipesan</h3>
                        <p class="text-xs text-stone-500">Harga dan nama menu terekam permanen (snapshot historis)</p>
                    </div>
                    <span class="font-mono text-xs font-bold text-amber-800 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                        {{ $order->items->count() }} Variasi Item
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-stone-50 border-b border-stone-200 text-stone-500 font-semibold text-xs uppercase tracking-wider">
                                <th class="py-3 px-6">Nama Menu (Snapshot)</th>
                                <th class="py-3 px-6 text-right">Harga Satuan</th>
                                <th class="py-3 px-6 text-center">Jumlah</th>
                                <th class="py-3 px-6 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="py-4 px-6">
                                    <strong class="font-bold text-stone-900 block">{{ $item->menu_name }}</strong>
                                    @if($item->notes)
                                        <span class="text-xs text-amber-700 italic block mt-0.5">Catatan: "{{ $item->notes }}"</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right text-stone-700 font-mono">
                                    Rp{{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-stone-800">
                                    {{ $item->quantity }}x
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-stone-900 font-mono">
                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-stone-50 border-t-2 border-stone-200 font-bold">
                                <td colspan="3" class="py-4 px-6 text-stone-700 uppercase tracking-wider text-xs">
                                    Total Tagihan Pesanan
                                </td>
                                <td class="py-4 px-6 text-right text-lg text-amber-900 font-black font-mono">
                                    Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes or Customer instructions -->
            @if($order->notes)
            <div class="bg-amber-50/60 rounded-3xl p-5 border border-amber-200/70">
                <span class="text-xs font-bold text-amber-900 uppercase tracking-wider block mb-1">Catatan Tambahan Pelanggan</span>
                <p class="text-sm text-amber-950 font-medium">"{{ $order->notes }}"</p>
            </div>
            @endif
        </div>

        <!-- Sidebar Details: Canteen, Customer & Payment Status -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm space-y-4">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Status Pesanan Terkini</span>
                
                <div class="flex items-center gap-3">
                    @php
                        $badgeClasses = match($order->status) {
                            'Selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'Dibatalkan' => 'bg-rose-100 text-rose-800 border-rose-300',
                            'Siap diambil' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'Disiapkan' => 'bg-amber-100 text-amber-800 border-amber-300',
                            default => 'bg-stone-100 text-stone-800 border-stone-300',
                        };
                    @endphp
                    <span class="px-3.5 py-1.5 rounded-full text-sm font-bold border {{ $badgeClasses }}">
                        {{ $order->status }}
                    </span>
                    <span class="text-xs text-stone-400">
                        {{ $order->updated_at->diffForHumans() }}
                    </span>
                </div>

                <div class="pt-4 border-t border-stone-100 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Waktu Pemesanan:</span>
                        <span class="font-bold text-stone-800">{{ $order->created_at->format('d M Y, H:i:s') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Tracking Token:</span>
                        <span class="font-mono text-stone-600 truncate max-w-[140px]">{{ $order->access_token }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Card -->
            <div class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm space-y-4">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Informasi Pembayaran</span>

                <div class="flex items-center justify-between">
                    <div>
                        <strong class="text-base font-black text-stone-900 block">{{ strtoupper($order->payment_method) }}</strong>
                        <span class="text-xs text-stone-500">
                            {{ $order->payment_method === 'qris' ? 'Pembayaran Digital QRIS' : 'Tunai Langsung di Kasir Kantin' }}
                        </span>
                    </div>
                    @if($order->payment_status === 'Lunas')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            ✓ Lunas
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            ⏳ Belum Bayar
                        </span>
                    @endif
                </div>

                @if($order->payment)
                <div class="pt-3 border-t border-stone-100 space-y-1.5 text-xs text-stone-600">
                    <div class="flex justify-between">
                        <span>Nominal Terbayar:</span>
                        <span class="font-bold text-stone-900">Rp{{ number_format($order->payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Waktu Pembayaran:</span>
                        <span class="font-medium">{{ $order->payment->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Referensi:</span>
                        <span class="font-mono text-stone-500">{{ $order->payment->payment_reference ?? 'N/A' }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Kantin & Seller Card -->
            <div class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm space-y-3">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Kantin & Pengelola</span>

                @if($order->canteen)
                    <div>
                        <strong class="text-base font-bold text-stone-900 block">{{ $order->canteen->name }}</strong>
                        <span class="text-xs text-stone-500 block">📍 {{ $order->canteen->campus_location }}</span>
                    </div>
                    @if($order->canteen->seller)
                        <div class="text-xs pt-2 border-t border-stone-100">
                            <span class="text-stone-500">Penjual Bertanggung Jawab:</span>
                            <div class="font-semibold text-stone-800 mt-0.5">{{ $order->canteen->seller->name }} ({{ '@' . $order->canteen->seller->username }})</div>
                        </div>
                    @endif
                @else
                    <p class="text-xs text-stone-400">Data kantin tidak tersedia.</p>
                @endif
            </div>

            <!-- Customer Card -->
            <div class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm space-y-3">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Pemesan (Customer)</span>

                <div>
                    <strong class="text-base font-bold text-stone-900 block">{{ $order->customer_name }}</strong>
                    <span class="text-xs text-stone-500 block">📞 {{ $order->customer_phone ?? '-' }}</span>
                </div>

                @if($order->customer)
                <div class="pt-2 border-t border-stone-100 flex items-center justify-between text-xs">
                    <span class="text-stone-500">Civitas: <strong class="text-stone-700 capitalize">{{ $order->customer->user_type }}</strong></span>
                    <a href="{{ route('admin.customers.show', $order->customer) }}" class="text-amber-700 font-bold hover:underline">
                        Lihat Profil ↗
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
