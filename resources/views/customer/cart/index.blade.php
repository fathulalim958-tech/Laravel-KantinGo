@extends('layouts.customer')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight">Keranjang Belanja</h1>
        @if(!empty($cartItems))
            <form action="{{ route('customer.cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan keranjang belanja Anda?')">
                @csrf
                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline">
                    Kosongkan Keranjang
                </button>
            </form>
        @endif
    </div>

    <!-- Canteen Conflict Modal Dialog -->
    @if(session('canteen_conflict'))
        @php $conflict = session('canteen_conflict'); @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-stone-100 text-center space-y-4">
                <div class="w-14 h-14 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center mx-auto text-2xl">
                    ⚠️
                </div>
                <h3 class="text-lg font-black text-stone-900">Ganti Stan Kantin?</h3>
                <p class="text-xs text-stone-600 leading-relaxed">
                    Keranjang Anda saat ini berisi produk dari <strong>{{ $conflict['current_canteen_name'] }}</strong>. Sesuai aturan kampus, satu checkout hanya boleh dari <strong>satu kantin</strong>.
                </p>
                <p class="text-xs text-amber-800 font-semibold bg-amber-50 p-2.5 rounded-xl border border-amber-200">
                    Apakah Anda ingin mengosongkan keranjang lama dan beralih memesan <strong>{{ $conflict['new_menu_name'] }}</strong> dari <strong>{{ $conflict['new_canteen_name'] }}</strong>?
                </p>
                <div class="flex items-center justify-center gap-2 pt-2">
                    <a href="{{ route('customer.cart') }}" class="px-4 py-2.5 rounded-xl bg-stone-100 text-stone-700 text-xs font-bold hover:bg-stone-200 transition-colors">
                        Batal
                    </a>
                    <form action="{{ route('customer.cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $conflict['new_menu_id'] }}">
                        <input type="hidden" name="switch_canteen" value="1">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md shadow-amber-600/20 transition-all">
                            Ya, Ganti Kantin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if(empty($cartItems))
        <!-- Empty Cart State -->
        <div class="bg-white rounded-3xl p-12 text-center border border-stone-200/80 max-w-md mx-auto shadow-sm">
            <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-700 flex items-center justify-center mx-auto mb-4 text-3xl">
                🛒
            </div>
            <h2 class="text-xl font-black text-stone-900">Keranjang Belanja Kosong</h2>
            <p class="text-xs text-stone-500 mt-1 max-w-xs mx-auto">
                Anda belum memilih menu makanan atau minuman dari stan kantin manapun.
            </p>
            <a href="{{ route('customer.canteens') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md shadow-amber-600/20 transition-all">
                <span>Jelajahi Stan Kantin Sekarang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    @else
        <!-- Cart Items & Checkout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Items list -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Canteen Identifier -->
                @if($canteen)
                    <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $canteen->logo_url }}" alt="" class="w-10 h-10 rounded-xl object-contain bg-white p-1 border">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">Kantin Terpilih</span>
                                <h3 class="font-bold text-stone-900 text-sm">{{ $canteen->name }}</h3>
                                <p class="text-[11px] text-stone-500">📍 {{ $canteen->campus_location }}</p>
                            </div>
                        </div>
                        <a href="{{ route('customer.canteens.show', $canteen) }}" class="text-xs font-bold text-amber-700 hover:underline shrink-0">
                            + Tambah Menu
                        </a>
                    </div>
                @endif

                <!-- Items Form -->
                <div class="bg-white rounded-3xl border border-stone-200/80 shadow-sm overflow-hidden p-4 sm:p-6">
                    <form action="{{ route('customer.cart.update') }}" method="POST">
                        @csrf
                        <div class="divide-y divide-stone-100">
                            @foreach($cartItems as $item)
                                <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $item['menu']->image_url }}" alt="" class="w-14 h-14 rounded-2xl object-contain bg-amber-50 p-2 border border-amber-100 shrink-0">
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-stone-900 text-sm truncate">{{ $item['menu']->name }}</h4>
                                            <span class="text-xs text-stone-500 block">{{ $item['menu']->formatted_price }} / porsi</span>
                                            <span class="text-[11px] text-amber-800 font-semibold">Subtotal: Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <label class="text-[11px] text-stone-400 font-medium hidden sm:inline">Porsi:</label>
                                        <input type="number" name="qty[{{ $item['menu']->id }}]" value="{{ $item['quantity'] }}" min="0" max="{{ $item['menu']->stock }}"
                                               class="w-16 px-2.5 py-1.5 rounded-xl border border-stone-200 text-center text-xs font-bold focus:outline-none focus:ring-2 focus:ring-amber-500/20">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5 pt-4 border-t border-stone-100 flex items-center justify-between">
                            <span class="text-[11px] text-stone-400">Ubah porsi ke 0 untuk menghapus menu.</span>
                            <button type="submit" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold transition-colors">
                                Perbarui Jumlah Porsi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Checkout Summary -->
            <div class="space-y-4">
                <div class="bg-white rounded-3xl border border-stone-200/80 shadow-sm p-6 space-y-4 sticky top-20">
                    <h3 class="font-black text-stone-900 text-lg border-b border-stone-100 pb-3">Ringkasan Pembayaran</h3>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-stone-600">
                            <span>Subtotal Pesanan</span>
                            <span class="font-semibold">Rp{{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-stone-600">
                            <span>Biaya Layanan Kampus</span>
                            <span class="font-semibold text-emerald-600">Gratis (Rp0)</span>
                        </div>
                        <div class="pt-2 border-t border-stone-100 flex items-center justify-between text-stone-900">
                            <span class="font-bold text-sm">Total Bayar</span>
                            <span class="font-black text-lg text-amber-700">Rp{{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <form action="{{ route('customer.checkout.process') }}" method="POST" class="space-y-3 pt-3 border-t border-stone-100">
                        @csrf
                        <input type="hidden" name="checkout_key" value="{{ $checkoutKey }}">

                        <div>
                            <label for="nama_pengambil" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Nama Pemesan / Pengambil <span class="text-rose-500">*</span></label>
                            <input type="text" id="nama_pengambil" name="nama_pengambil" required
                                   value="{{ old('nama_pengambil', auth()->user()->name) }}"
                                   placeholder="Contoh: Budi Santoso"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                            @error('nama_pengambil')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="customer_type" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Kelas / Prodi / Ruangan</label>
                            <input type="text" id="customer_type" name="customer_type"
                                   value="{{ old('customer_type', auth()->user()->identifier_number ? (ucfirst(auth()->user()->user_type) . ' - ' . auth()->user()->identifier_number) : '') }}"
                                   placeholder="Contoh: Mahasiswa TI 05TPLP001"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                        </div>

                        <div>
                            <label for="metode_pembayaran" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Metode Pembayaran <span class="text-rose-500">*</span></label>
                            <select id="metode_pembayaran" name="metode_pembayaran" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-semibold text-stone-800">
                                <option value="Tunai" {{ old('metode_pembayaran') === 'Tunai' ? 'selected' : '' }}>💵 Tunai (Bayar saat ambil di kantin)</option>
                                <option value="QRIS" {{ old('metode_pembayaran') === 'QRIS' ? 'selected' : '' }}>📱 QRIS (Simulasi Tugas Kampus)</option>
                            </select>
                            <span class="text-[10px] text-stone-400 block mt-1">Pembayaran QRIS hanya simulasi tugas dan tidak memindahkan uang sungguhan.</span>
                        </div>

                        <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold text-sm shadow-lg shadow-amber-600/25 transition-all transform active:scale-[0.99] flex items-center justify-center gap-2 mt-4">
                            <span>Konfirmasi & Buat Pesanan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
