@extends('layouts.seller')

@section('title', 'Detail Pesanan: ' . $order->order_code)
@section('page_title', 'Rincian Pesanan ' . $order->order_code)
@section('page_subtitle', 'Informasi pengambilan, menu, dan status pembayaran')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('seller.orders.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-600 hover:text-amber-800 bg-white px-3 py-1.5 rounded-xl border border-stone-200">
            ← Kembali ke Antrean Pesanan
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-sm space-y-6">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-stone-100 pb-5">
            <div>
                <span class="text-xs font-bold text-stone-400 uppercase tracking-widest block">KODE PESANAN</span>
                <h2 class="text-3xl font-black text-amber-800">{{ $order->order_code }}</h2>
                <span class="text-xs text-stone-500">Dibuat pada: {{ $order->created_at->format('d/m/Y H:i:s') }} WIB</span>
            </div>

            <div class="text-right">
                <span class="text-xs font-bold text-stone-400 uppercase tracking-widest block mb-1">STATUS SAAT INI</span>
                @php
                    $statusBadges = [
                        'Diterima' => 'bg-amber-100 text-amber-900 border-amber-300',
                        'Disiapkan' => 'bg-blue-100 text-blue-900 border-blue-300',
                        'Siap diambil' => 'bg-emerald-100 text-emerald-900 border-emerald-300 animate-pulse',
                        'Selesai' => 'bg-stone-100 text-stone-600 border-stone-200',
                    ];
                @endphp
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase border {{ $statusBadges[$order->status] ?? 'bg-stone-100' }}">
                    {{ $order->status }}
                </span>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-amber-50/50 border border-amber-100 text-xs">
            <div>
                <span class="text-stone-400 block font-medium">Pemesan</span>
                <strong class="text-stone-800 text-sm block mt-0.5">{{ $order->customer_name }}</strong>
            </div>
            <div>
                <span class="text-stone-400 block font-medium">Identitas / Kelas</span>
                <strong class="text-stone-800 text-sm block mt-0.5">{{ $order->customer_type }}</strong>
            </div>
            <div>
                <span class="text-stone-400 block font-medium">Metode Pembayaran</span>
                <strong class="text-stone-800 text-sm block mt-0.5">{{ $order->payment_method }}</strong>
            </div>
            <div>
                <span class="text-stone-400 block font-medium">Status Bayar</span>
                <span class="inline-block mt-0.5 px-2.5 py-0.5 rounded-full font-bold {{ $order->payment_status === 'Lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $order->payment_status }}
                </span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="border border-stone-200 rounded-2xl overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-stone-600 uppercase font-bold">
                        <th class="p-3.5">Menu</th>
                        <th class="p-3.5 text-right">Harga Satuan</th>
                        <th class="p-3.5 text-center">Porsi</th>
                        <th class="p-3.5 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @php
                        $snapshot = is_array($order->detail_menu_snapshot) ? $order->detail_menu_snapshot : json_decode($order->detail_menu_snapshot, true);
                    @endphp
                    @foreach($snapshot ?? [] as $it)
                        <tr>
                            <td class="p-3.5 font-bold text-stone-900">{{ $it['nama_menu'] ?? $it['name'] }}</td>
                            <td class="p-3.5 text-right text-stone-600">Rp{{ number_format($it['harga'] ?? 0, 0, ',', '.') }}</td>
                            <td class="p-3.5 text-center font-bold text-stone-800">{{ $it['jumlah'] ?? $it['qty'] }}</td>
                            <td class="p-3.5 text-right font-black text-stone-900">Rp{{ number_format(($it['harga'] ?? 0) * ($it['jumlah'] ?? 1), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-amber-50/60 font-black text-sm text-stone-900 border-t border-amber-200">
                        <td colspan="3" class="p-3.5 text-right">Total Tagihan</td>
                        <td class="p-3.5 text-right text-amber-800">{{ $order->formatted_total }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Action Row -->
        <div class="flex items-center justify-between pt-2">
            <span class="text-xs text-stone-400">Pastikan rincian pesanan sesuai sebelum memproses.</span>
            @if($order->status === 'Diterima')
                <form action="{{ route('seller.orders.status', $order) }}" method="POST">
                    @csrf
                    <input type="hidden" name="expected" value="Diterima">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition-all">
                        Mulai Disiapkan
                    </button>
                </form>
            @elseif($order->status === 'Disiapkan')
                <form action="{{ route('seller.orders.status', $order) }}" method="POST">
                    @csrf
                    <input type="hidden" name="expected" value="Disiapkan">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition-all">
                        Tandai Siap Diambil
                    </button>
                </form>
            @elseif($order->status === 'Siap diambil')
                <form action="{{ route('seller.orders.status', $order) }}" method="POST" onsubmit="return confirm('{{ $order->payment_method === 'Tunai' && $order->payment_status !== 'Lunas' ? 'Pastikan uang tunai sebesar ' . $order->formatted_total . ' sudah diterima. Selesaikan pesanan?' : 'Selesaikan pesanan?' }}')">
                    @csrf
                    <input type="hidden" name="expected" value="Siap diambil">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md transition-all"
                            {{ ($order->payment_method === 'QRIS' && $order->payment_status !== 'Lunas') ? 'disabled' : '' }}>
                        {{ $order->payment_status === 'Lunas' ? 'Selesaikan Pesanan' : 'Konfirmasi Uang Tunai & Selesaikan' }}
                    </button>
                </form>
            @else
                <span class="px-4 py-2 bg-stone-100 text-stone-500 rounded-xl text-xs font-bold">Pesanan Telah Selesai</span>
            @endif
        </div>
    </div>
</div>
@endsection
