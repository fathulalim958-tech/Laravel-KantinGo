@extends('layouts.customer')

@section('title', 'Simulasi Pembayaran QRIS · ' . $order->order_code)

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/80 shadow-xl text-center space-y-5">
        <!-- Badge Simulasi -->
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300">
            <span>⚠️ SIMULASI · BUKAN PEMBAYARAN ASLI</span>
        </div>

        <div>
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Stan Kantin: {{ $order->canteen->name }}</span>
            <h1 class="text-2xl font-black text-stone-900 mt-0.5">Pesanan {{ $order->order_code }}</h1>
            <p class="text-xs text-stone-600 mt-1">Pemesan: <strong>{{ $order->customer_name }}</strong> ({{ $order->customer_type }})</p>
        </div>

        <!-- Mock QR Code SVG -->
        <div class="p-4 bg-amber-50/50 rounded-2xl border border-amber-200 inline-block mx-auto shadow-inner">
            <svg viewBox="0 0 210 210" class="w-56 h-56 mx-auto" role="img" aria-label="Mock QRIS">
                <rect width="210" height="210" fill="white" rx="12"/>
                @for($y = 0; $y < 25; $y++)
                    @for($x = 0; $x < 25; $x++)
                        @if((hexdec(substr(hash('sha256', 'unpam-qris-' . $x . '-' . $y), 0, 2)) % 2) === 0)
                            <rect x="{{ 5 + $x * 8 }}" y="{{ 5 + $y * 8 }}" width="7" height="7" fill="#451a03" rx="1.5"/>
                        @endif
                    @endfor
                @endfor
                <rect x="15" y="88" width="180" height="34" fill="#fef3c7" rx="6" stroke="#f59e0b" stroke-width="1.5"/>
                <text x="105" y="110" text-anchor="middle" font-size="13" font-weight="900" fill="#78350f">DEMO / BUKAN QRIS</text>
            </svg>
        </div>

        <!-- Total Price -->
        <div class="bg-stone-50 rounded-2xl p-3 border border-stone-100">
            <span class="text-xs text-stone-500 block">Total Tagihan</span>
            <strong class="text-2xl font-black text-amber-700">Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong>
        </div>

        <p class="text-[11px] text-stone-500 leading-relaxed max-w-xs mx-auto">
            Pola kode di atas adalah mock QR ilustrasi tugas akademik. Jangan mentransfer uang sungguhan.
        </p>

        @if($order->payment_status === 'Lunas')
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                ✓ Simulasi pembayaran sudah lunas. Pesanan Anda sedang disiapkan oleh penjual.
            </div>
            <a href="{{ route('orders.track', $order->access_token) }}" class="block w-full py-3 px-4 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition-all">
                Lihat Status Pelacakan Pesanan
            </a>
        @else
            <!-- Action Simulation Form -->
            <form action="{{ route('orders.qris.pay', $order->access_token) }}" method="POST" onsubmit="return confirm('Simulasikan pembayaran QRIS berhasil? Data akan diperbarui untuk tugas kampus.')">
                @csrf
                <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-bold text-sm shadow-lg shadow-emerald-600/25 transition-all transform active:scale-[0.99] flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simulasi: Bayar Berhasil</span>
                </button>
            </form>

            <a href="{{ route('orders.track', $order->access_token) }}" class="block text-xs font-bold text-stone-500 hover:text-stone-800">
                Kembali ke Status Pesanan
            </a>
        @endif
    </div>
</div>
@endsection
