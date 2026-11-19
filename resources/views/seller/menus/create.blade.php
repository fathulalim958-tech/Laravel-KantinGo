@extends('layouts.seller')

@section('title', 'Tambah Menu Baru · ' . $canteen->name)
@section('page_title', 'Tambah Menu Baru')
@section('page_subtitle', 'Tambahkan menu makanan atau minuman baru ke stan kantin Anda')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('seller.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Nama Menu -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Nama Menu <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Nasi Ayam Sambal Matah"
                       class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori & Harga -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kategori" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Kategori <span class="text-rose-500">*</span></label>
                    <select id="kategori" name="kategori" required
                            class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-semibold">
                        <option value="Makanan" {{ old('kategori') === 'Makanan' ? 'selected' : '' }}>🍛 Makanan</option>
                        <option value="Minuman" {{ old('kategori') === 'Minuman' ? 'selected' : '' }}>🥤 Minuman</option>
                    </select>
                </div>

                <div>
                    <label for="price" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" id="price" name="price" value="{{ old('price', 10000) }}" min="500" max="10000000" step="500" required
                           class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-bold text-amber-800">
                    @error('price')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Stok Porsi -->
            <div>
                <label for="stock" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Kuota Stok Tersedia (Porsi) <span class="text-rose-500">*</span></label>
                <input type="number" id="stock" name="stock" value="{{ old('stock', 20) }}" min="0" max="10000" required
                       class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-bold text-stone-800">
                <span class="text-[11px] text-stone-400 mt-1 block">Menu dengan stok 0 otomatis ditandai habis di katalog pembeli.</span>
                @error('stock')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Deskripsi / Komposisi Singkat</label>
                <textarea id="description" name="description" rows="2"
                          placeholder="Jelaskan lauk, tingkat kepedasan, atau bahan menu..."
                          class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">{{ old('description') }}</textarea>
            </div>

            <!-- Foto Menu & Preview -->
            <div>
                <label for="image" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Foto Menu (Opsional)</label>
                <div class="flex items-center gap-4 mb-2">
                    <img id="image-preview" src="{{ asset('assets/menu/default.svg') }}" alt="Preview" class="w-16 h-16 rounded-2xl object-contain bg-amber-50 p-2 border">
                    <span class="text-xs text-stone-500">Jika tidak mengunggah foto, sistem akan menggunakan ikon ilustrasi bawaan.</span>
                </div>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)"
                       class="w-full px-4 py-2 rounded-xl border border-stone-200 text-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                <span class="text-[11px] text-stone-400 block mt-1">Format: JPG, PNG, WebP. Maksimal 2 MB.</span>
                @error('image')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-stone-100">
                <a href="{{ route('seller.menus.index') }}" class="px-5 py-2.5 text-stone-600 hover:bg-stone-100 rounded-xl text-sm font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold shadow-md shadow-amber-600/20 transition-all">
                    Simpan Menu Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const preview = document.getElementById('image-preview');
            preview.src = URL.createObjectURL(file);
        }
    }
</script>
@endsection
