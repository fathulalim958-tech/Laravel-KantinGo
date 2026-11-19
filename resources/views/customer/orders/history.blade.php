@extends('layouts.customer')

@section('title', 'Riwayat Pesanan Saya')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight">Riwayat Pesanan</h1>
            <p class="text-xs text-stone-500 mt-1">Daftar pesanan makanan & minuman Anda di seluruh kantin UNPAM</p>
        </div>

        <a href="{{ route('customer.canteens') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md shadow-amber-600/20 transition-all self-start sm:self-auto">
            <span>+ Pesan Makanan Baru</span>
        </a>
    </div>

    <!-- Filter Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
        <a href="{{ route('customer.orders.index') }}" class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all {{ !request('status') ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
            Semua
        </a>
        <a href="{{ route('customer.orders.index', ['status' => 'Diterima']) }}" class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all {{ request('status') === 'Diterima' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
            Diterima
        </a>
        <a href="{{ route('customer.orders.index', ['status' => 'Disiapkan']) }}" class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all {{ request('status') === 'Disiapkan' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
            Disiapkan
        </a>
        <a href="{{ route('customer.orders.index', ['status' => 'Siap diambil']) }}" class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all {{ request('status') === 'Siap diambil' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
            Siap Diambil
        </a>
        <a href="{{ route('customer.orders.index', ['status' => 'Selesai']) }}" class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all {{ request('status') === 'Selesai' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-amber-50 border border-stone-200' }}">
            Selesai
        </a>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        @forelse($orders as $order)
            <div class="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-2.5">
                        <span class="font-black text-base text-stone-900">{{ $order->order_code }}</span>
                        @php
                            $statusColors = [
                                'Diterima' => 'bg-amber-100 text-amber-900 border-amber-300',
                                'Disiapkan' => 'bg-blue-100 text-blue-900 border-blue-300',
                                'Siap diambil' => 'bg-emerald-100 text-emerald-900 border-emerald-300 animate-pulse',
                                'Selesai' => 'bg-stone-100 text-stone-700 border-stone-200',
                            ];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $statusColors[$order->status] ?? 'bg-stone-100' }}">
                            {{ $order->status }}
                        </span>
                        <span class="text-xs text-stone-400">· {{ $order->created_at->format('d/m/Y H:i') }} WIB</span>
                    </div>

                    <div class="text-xs text-stone-600 flex items-center gap-2">
                        <span class="font-semibold text-amber-800">🏪 {{ $order->canteen->name }}</span>
                        <span>·</span>
                        <span class="text-stone-500">{{ $order->payment_method }} ({{ $order->payment_status }})</span>
                    </div>

                    <p class="text-xs text-stone-700 font-medium">
                        {{ $order->items_summary }}
                    </p>
                </div>

                <div class="flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-t-0 pt-3 sm:pt-0 border-stone-100 gap-2">
                    <strong class="text-base font-black text-amber-800">{{ $order->formatted_total }}</strong>
                    <div class="flex items-center gap-2">
                        @php
                            $isCash = in_array(strtolower($order->payment_method), ['tunai', 'cash']);
                        @endphp
                        @if($order->status === 'Siap diambil' && !$isCash)
                            <form action="{{ route('orders.complete', $order->access_token) }}" method="POST" onsubmit="return confirm('Konfirmasi pesanan {{ $order->order_code }} sudah diambil?')">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Sudah Diambil</span>
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('orders.track', $order->access_token) }}" class="px-3.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold transition-colors">
                            Lacak →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-stone-200">
                <div class="text-3xl mb-2">📋</div>
                <h3 class="text-base font-bold text-stone-800">Belum ada pesanan</h3>
                <p class="text-xs text-stone-500 mt-1">Pesanan Anda akan muncul di sini setelah Anda melakukan checkout.</p>
                <a href="{{ route('customer.canteens') }}" class="inline-block mt-4 text-xs font-bold text-amber-700 hover:underline">
                    Mulai Pesan Sekarang
                </a>
            </div>
        @endforelse
    </div>

    @if($orders->hasPages())
        <div class="pt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
