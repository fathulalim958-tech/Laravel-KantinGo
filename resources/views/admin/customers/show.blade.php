@extends('layouts.admin')

@section('title', 'Detail Pelanggan: ' . $customer->name)
@section('page_title', 'Profil & Riwayat Pelanggan')
@section('page_subtitle', 'Rincian data akun dan jejak transaksi pesanan di seluruh kantin')

@section('content')
<div class="space-y-6">
    <!-- Action buttons -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-600 hover:text-stone-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Pelanggan
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition shadow-sm">
                Edit Akun
            </a>
            <form action="{{ route('admin.customers.toggle-status', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Ubah status akun pelanggan ini?')">
                @csrf
                <button type="submit" class="px-4 py-2 border border-stone-300 hover:bg-stone-50 text-stone-700 text-sm font-semibold rounded-xl transition">
                    {{ $customer->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Customer Overview Profile Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Info Card -->
        <div class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm md:col-span-2">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                <img src="{{ $customer->avatar_url }}" alt="{{ $customer->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-amber-300 bg-amber-50 shadow-sm">
                <div class="flex-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-2xl font-black text-stone-900">{{ $customer->name }}</h2>
                        @if($customer->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                ● Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                ● Nonaktif
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-stone-500 mt-1 font-mono">{{ '@' . $customer->username }} · {{ $customer->email }}</p>

                    <div class="mt-4 flex flex-wrap items-center gap-3 text-xs">
                        @if($customer->user_type === 'mahasiswa')
                            <span class="px-3 py-1 rounded-full font-bold bg-blue-100 text-blue-800">🎓 Mahasiswa UNPAM</span>
                        @elseif($customer->user_type === 'dosen')
                            <span class="px-3 py-1 rounded-full font-bold bg-purple-100 text-purple-800">👨‍🏫 Dosen UNPAM</span>
                        @else
                            <span class="px-3 py-1 rounded-full font-bold bg-amber-100 text-amber-800">💼 Staf Kampus</span>
                        @endif

                        @if($customer->identifier_number)
                            <span class="px-3 py-1 rounded-full font-bold bg-stone-100 text-stone-700 font-mono">
                                NIM/NIP: {{ $customer->identifier_number }}
                            </span>
                        @endif

                        @if($customer->phone)
                            <span class="px-3 py-1 rounded-full font-bold bg-stone-100 text-stone-700">
                                📞 {{ $customer->phone }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-stone-100 text-xs text-stone-400">
                Terdaftar sejak: {{ $customer->created_at->format('d M Y, H:i') }} ({{ $customer->created_at->diffForHumans() }})
            </div>
        </div>

        <!-- Metric Card -->
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl p-6 border border-amber-200/60 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-amber-800 uppercase tracking-wider block">Total Pengeluaran</span>
                <strong class="text-3xl font-black text-amber-900 block mt-2">Rp{{ number_format($totalSpent, 0, ',', '.') }}</strong>
                <span class="text-xs text-amber-700 mt-1 block">Dari pesanan yang berstatus lunas</span>
            </div>

            <div class="mt-6 pt-4 border-t border-amber-200/50 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-stone-500 block">Total Pesanan</span>
                    <strong class="text-xl font-bold text-stone-800 block">{{ $totalOrders }} kali transaksi</strong>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-200/60 flex items-center justify-center text-amber-800">
                    🛍️
                </div>
            </div>
        </div>
    </div>

    <!-- Order History Table -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-stone-100">
            <h3 class="text-base font-bold text-stone-900">Riwayat Pesanan Pelanggan</h3>
            <p class="text-xs text-stone-500 mt-0.5">Seluruh pesanan makanan/minuman yang dilakukan oleh akun ini</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-stone-500 font-semibold text-xs uppercase tracking-wider">
                        <th class="py-4 px-6">Kode Pesanan</th>
                        <th class="py-4 px-6">Kantin</th>
                        <th class="py-4 px-6">Waktu Transaksi</th>
                        <th class="py-4 px-6">Total Harga</th>
                        <th class="py-4 px-6">Metode & Status Bayar</th>
                        <th class="py-4 px-6">Status Pesanan</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($customer->orders as $o)
                    <tr class="hover:bg-amber-50/40 transition">
                        <td class="py-4 px-6 font-mono font-bold text-amber-900">
                            {{ $o->order_code }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-bold text-stone-800 block">{{ $o->canteen->name ?? 'Kantin Tidak Diketahui' }}</span>
                            <span class="text-xs text-stone-400 block">{{ $o->canteen->campus_location ?? '' }}</span>
                        </td>
                        <td class="py-4 px-6 text-stone-600 text-xs">
                            {{ $o->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="py-4 px-6 font-bold text-stone-900">
                            Rp{{ number_format($o->total_price, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-semibold text-xs text-stone-700 block">{{ strtoupper($o->payment_method) }}</span>
                            @if($o->payment_status === 'Lunas')
                                <span class="inline-flex items-center text-[11px] font-bold text-emerald-700">● Lunas</span>
                            @else
                                <span class="inline-flex items-center text-[11px] font-bold text-amber-600">○ Belum Bayar</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @php
                                $badgeClasses = match($o->status) {
                                    'Selesai' => 'bg-emerald-100 text-emerald-800',
                                    'Dibatalkan' => 'bg-rose-100 text-rose-800',
                                    'Siap diambil' => 'bg-blue-100 text-blue-800',
                                    'Disiapkan' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-stone-100 text-stone-800',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeClasses }}">
                                {{ $o->status }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('admin.transactions.show', $o) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-stone-100 hover:bg-amber-100 text-stone-700 hover:text-amber-900 transition">
                                Rincian
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-stone-400">
                            Pelanggan ini belum pernah melakukan pemesanan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
