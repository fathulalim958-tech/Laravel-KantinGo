@extends('layouts.customer')

@section('title', 'Pelacakan Pesanan: ' . $order->order_code)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Pickup Ticket Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/80 shadow-xl text-center space-y-6">
        <!-- Status Badge -->
        <div>
            @php
                $statusColors = [
                    'Diterima' => 'bg-amber-100 text-amber-900 border-amber-300',
                    'Disiapkan' => 'bg-blue-100 text-blue-900 border-blue-300',
                    'Siap diambil' => 'bg-emerald-100 text-emerald-900 border-emerald-300 animate-pulse',
                    'Selesai' => 'bg-stone-100 text-stone-700 border-stone-300',
                ];
            @endphp
            <span id="badge-status" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $statusColors[$order->status] ?? 'bg-stone-100 text-stone-700' }}">
                Status: {{ $order->status }}
            </span>
        </div>

        <!-- Big Pickup Box -->
        <div class="bg-gradient-to-br from-amber-600 to-amber-800 text-white p-6 rounded-3xl shadow-lg shadow-amber-800/20 max-w-sm mx-auto">
            <span class="text-xs font-bold uppercase tracking-widest text-amber-200 block mb-1">KODE PENGAMBILAN</span>
            <strong class="text-4xl sm:text-5xl font-black tracking-tight block">{{ $order->order_code }}</strong>
            <span class="text-xs text-amber-100 block mt-2">Tunjukkan kode ini kepada penjual kantin saat mengambil pesanan</span>
        </div>

        <!-- Progress Steps -->
        @php
            $statuses = ['Diterima', 'Disiapkan', 'Siap diambil', 'Selesai'];
            $currentIndex = array_search($order->status, $statuses);
            if ($currentIndex === false) $currentIndex = 0;
        @endphp
        <div class="py-2">
            <div class="grid grid-cols-4 gap-2 text-center text-xs font-bold">
                @foreach($statuses as $idx => $st)
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs mb-1.5 transition-all {{ $idx <= $currentIndex ? 'bg-amber-600 text-white shadow-md' : 'bg-stone-100 text-stone-400' }}">
                            {{ $idx + 1 }}
                        </div>
                        <span class="{{ $idx <= $currentIndex ? 'text-amber-900 font-black' : 'text-stone-400' }} text-[11px] leading-tight">
                            {{ $st }}
                        </span>
                    </div>
                @endforeach
            </div>
            <!-- Progress Line Bar -->
            <div class="w-full bg-stone-100 h-2 rounded-full overflow-hidden mt-3">
                <div id="progress-bar" class="bg-amber-600 h-full transition-all duration-700" style="width: {{ (($currentIndex + 1) / 4) * 100 }}%"></div>
            </div>
        </div>

        <!-- Order Summary Details -->
        <div class="bg-stone-50 rounded-2xl p-5 border border-stone-200/70 text-left space-y-3">
            <div class="flex items-center justify-between border-b border-stone-200 pb-2.5">
                <div>
                    <span class="text-[10px] font-bold uppercase text-stone-400">Stan Kantin</span>
                    <h4 class="font-bold text-stone-900 text-sm">{{ $order->canteen->name }}</h4>
                    <p class="text-xs text-stone-500">📍 {{ $order->canteen->campus_location }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase text-stone-400">Metode Bayar</span>
                    <p class="font-bold text-stone-800 text-xs">{{ $order->payment_method }}</p>
                    <span id="badge-payment" class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-black {{ $order->payment_status === 'Lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $order->payment_status }}
                    </span>
                </div>
            </div>

            <!-- Items -->
            <div class="space-y-1.5 text-xs text-stone-700">
                @php
                    $snapshot = is_array($order->detail_menu_snapshot) ? $order->detail_menu_snapshot : json_decode($order->detail_menu_snapshot, true);
                @endphp
                @foreach($snapshot ?? [] as $it)
                    <div class="flex items-center justify-between">
                        <span>{{ $it['nama_menu'] ?? $it['name'] ?? 'Menu' }} × {{ $it['jumlah'] ?? $it['qty'] ?? 1 }}</span>
                        <span class="font-semibold text-stone-900">Rp{{ number_format(($it['harga'] ?? 0) * ($it['jumlah'] ?? 1), 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Total -->
            <div class="pt-2 border-t border-stone-200 flex items-center justify-between font-black text-sm text-stone-900">
                <span>Total Pembayaran</span>
                <span class="text-amber-800 text-base">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        @php
            $isCash = in_array(strtolower($order->payment_method), ['tunai', 'cash']);
        @endphp

        <!-- Tombol Konfirmasi Selesai / Sudah Diambil (Khusus Non-Tunai / QRIS) -->
        @if(!$isCash)
        <div id="complete-box" class="{{ $order->status === 'Siap diambil' ? '' : 'hidden' }}">
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-3">
                <div class="flex items-center justify-center gap-2 text-emerald-800 text-xs font-bold">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Pesanan Anda sudah siap diambil di stan kantin!</span>
                </div>
                <form action="{{ route('orders.complete', $order->access_token) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa Anda telah mengambil pesanan ini di kantin?')">
                    @csrf
                    <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-bold text-sm shadow-lg shadow-emerald-600/25 transition-all transform active:scale-[0.99] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Pesanan Selesai / Sudah Diambil</span>
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($order->status === 'Siap diambil' && $isCash)
            <!-- Info Pengambilan Pesanan Tunai di Kasir -->
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-1">
                <div class="text-xs font-bold text-amber-900 flex items-center justify-center gap-1.5">
                    <span>💵 Pembayaran Tunai di Kasir</span>
                </div>
                <p class="text-xs text-amber-800 leading-relaxed">
                    Pesanan Anda sudah siap! Silakan tunjukkan kode <strong>{{ $order->order_code }}</strong> dan bayar tunai di kasir <strong>{{ $order->canteen->name }}</strong> saat mengambil makanan. Penjual akan menyelesaikan pesanan di kasir.
                </p>
            </div>
        @endif

        @if($order->payment_method === 'QRIS' && $order->payment_status !== 'Lunas')
            <a href="{{ route('orders.qris', $order->access_token) }}" class="block w-full py-3.5 px-4 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition-all">
                📱 Lanjutkan Simulasi Pembayaran QRIS
            </a>
        @endif

        <!-- Polling Indicator -->
        <div class="flex items-center justify-center gap-2 text-xs text-stone-500 pt-1">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span id="poll-indicator">Otomatis memeriksa pembaruan status setiap 8 detik...</span>
        </div>
    </div>
</div>

<script>
    const accessToken = "{{ $order->access_token }}";
    const statuses = ['Diterima', 'Disiapkan', 'Siap diambil', 'Selesai'];
    const badgeColors = {
        'Diterima': 'bg-amber-100 text-amber-900 border-amber-300',
        'Disiapkan': 'bg-blue-100 text-blue-900 border-blue-300',
        'Siap diambil': 'bg-emerald-100 text-emerald-900 border-emerald-300 animate-pulse',
        'Selesai': 'bg-stone-100 text-stone-700 border-stone-300'
    };

    async function checkOrderStatus() {
        try {
            const res = await fetch(`/api/orders/track/${accessToken}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;

            const data = await res.json();

            // Update badge status
            const badgeStatus = document.getElementById('badge-status');
            if (badgeStatus) {
                badgeStatus.textContent = 'Status: ' + data.status;
                badgeStatus.className = 'inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border ' + (badgeColors[data.status] || 'bg-stone-100 text-stone-700');
            }

            // Update progress bar
            const currentIndex = statuses.indexOf(data.status);
            if (currentIndex !== -1) {
                const percent = ((currentIndex + 1) / 4) * 100;
                const bar = document.getElementById('progress-bar');
                if (bar) bar.style.width = percent + '%';
            }

            // Update payment badge
            const badgePayment = document.getElementById('badge-payment');
            if (badgePayment) {
                badgePayment.textContent = data.payment_status;
                badgePayment.className = 'inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-black ' + (data.payment_status === 'Lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800');
            }

            // Update complete button visibility dynamically (khusus non-cash / QRIS)
            const completeBox = document.getElementById('complete-box');
            if (completeBox) {
                const isCash = ['tunai', 'cash'].includes((data.payment_method || '').toLowerCase());
                if (data.status === 'Siap diambil' && !isCash) {
                    completeBox.classList.remove('hidden');
                } else {
                    completeBox.classList.add('hidden');
                }
            }

            const indicator = document.getElementById('poll-indicator');
            if (indicator) {
                indicator.textContent = 'Terakhir dicek pukul ' + (data.updated_at || new Date().toLocaleTimeString()) + ' WIB';
            }
        } catch (e) {
            console.warn('Gagal memuat status live:', e);
        }
    }

    // Auto poll every 8 seconds
    setInterval(checkOrderStatus, 8000);
</script>
@endsection
