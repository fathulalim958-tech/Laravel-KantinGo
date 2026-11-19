@extends('layouts.admin')

@section('title', 'Tambah Kantin Baru')
@section('page_title', 'Daftarkan Kantin Baru')
@section('page_subtitle', 'Tambahkan stan kantin baru di lingkungan kampus Universitas Pamulang')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.canteens.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Nama Kantin -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Nama Kantin <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Kantin Berkah Viktor"
                       class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Seller Penanggung Jawab -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="seller_id" class="block text-xs font-bold uppercase tracking-wider text-stone-700">Penjual Penanggung Jawab <span class="text-rose-500">*</span></label>
                    <a href="{{ route('admin.sellers.create') }}" target="_blank" class="text-xs text-amber-700 hover:underline font-semibold">+ Buat Akun Seller Baru</a>
                </div>
                <select id="seller_id" name="seller_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">
                    <option value="">-- Pilih Akun Penjual --</option>
                    @foreach($availableSellers as $seller)
                        <option value="{{ $seller->id }}" {{ old('seller_id') == $seller->id ? 'selected' : '' }}>
                            {{ $seller->name }} ({{ $seller->username }} - {{ $seller->email }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-stone-500 mt-1">Hanya menampilkan penjual yang belum mengelola kantin manapun (Prinsip 1 Seller = 1 Kantin).</p>
                @error('seller_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi Kampus (Dropdown 3 Kategori: Viktor, Witana, Pusat) -->
            <div>
                <label for="campus_location" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Lokasi di Kampus UNPAM <span class="text-rose-500">*</span></label>
                <select id="campus_location" name="campus_location" required
                        class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm bg-white">
                    <option value="">-- Pilih Lokasi Kampus --</option>
                    <option value="Viktor" {{ (old('campus_location') == 'Viktor' || str_contains(old('campus_location', ''), 'Viktor')) ? 'selected' : '' }}>🏢 Viktor (Kampus 2 Viktor)</option>
                    <option value="Witana" {{ (old('campus_location') == 'Witana' || str_contains(old('campus_location', ''), 'Witana')) ? 'selected' : '' }}>🏛️ Witana (Kampus Witana Harja)</option>
                    <option value="Pusat" {{ (old('campus_location') == 'Pusat' || str_contains(old('campus_location', ''), 'Pusat')) ? 'selected' : '' }}>🏬 Pusat (Kampus 1 Pusat)</option>
                </select>
                @error('campus_location')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jam Operasional & Status Buka -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="opening_hours" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Jam Operasional</label>
                    <input type="text" id="opening_hours" name="opening_hours" value="{{ old('opening_hours', '07:30 - 17:00 WIB') }}"
                           placeholder="07:30 - 17:00 WIB"
                           class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Status Awal</label>
                    <label class="flex items-center gap-2 cursor-pointer mt-2.5">
                        <input type="checkbox" name="is_open" value="1" {{ old('is_open', true) ? 'checked' : '' }} class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
                        <span class="text-sm font-semibold text-stone-700">Kantin Langsung Dibuka</span>
                    </label>
                </div>
            </div>

            <!-- Deskripsi Kantin -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Deskripsi Singkat</label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Jelaskan menu andalan atau ciri khas kantin..."
                          class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">{{ old('description') }}</textarea>
            </div>

            <!-- Logo Kantin -->
            <div>
                <label for="logo" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Logo / Foto Kantin (Opsional)</label>
                <input type="file" id="logo" name="logo" accept="image/*"
                       class="w-full px-4 py-2 rounded-xl border border-stone-200 text-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                <span class="text-[11px] text-stone-400 block mt-1">Format: JPG, PNG, WebP, SVG. Maksimal 2 MB.</span>
                @error('logo')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-stone-100">
                <a href="{{ route('admin.canteens.index') }}" class="px-5 py-2.5 text-stone-600 hover:bg-stone-100 rounded-xl text-sm font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold shadow-md shadow-amber-600/20 transition-all">
                    Simpan & Aktifkan Kantin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
