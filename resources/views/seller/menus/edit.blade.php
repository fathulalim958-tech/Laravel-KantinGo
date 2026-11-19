@extends('layouts.seller')

@section('title', 'Edit Menu: ' . $menu->name)
@section('page_title', 'Perbarui Menu & Stok')
@section('page_subtitle', 'Kelola informasi harga, kuota stok, dan foto menu')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('seller.menus.update', $menu) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama Menu -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Nama Menu <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $menu->name) }}" required
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
                        <option value="Makanan" {{ old('kategori', $menu->kategori) === 'Makanan' ? 'selected' : '' }}>🍛 Makanan</option>
                        <option value="Minuman" {{ old('kategori', $menu->kategori) === 'Minuman' ? 'selected' : '' }}>🥤 Minuman</option>
                    </select>
                </div>

                <div>
                    <label for="price" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" id="price" name="price" value="{{ old('price', $menu->price) }}" min="500" max="10000000" step="500" required
                           class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-bold text-amber-800">
                    @error('price')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Stok Porsi -->
            <div>
                <label for="stock" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Kuota Stok Tersedia (Porsi) <span class="text-rose-500">*</span></label>
                <input type="number" id="stock" name="stock" value="{{ old('stock', $menu->stock) }}" min="0" max="10000" required
                       class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-bold text-stone-800">
                @error('stock')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Ketersediaan -->
            <div>
                <label class="flex items-center gap-2 cursor-pointer mt-1">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $menu->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
                    <span class="text-sm font-semibold text-stone-700">Tampilkan Menu di Katalog Pembeli</span>
                </label>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Deskripsi / Komposisi Singkat</label>
                <textarea id="description" name="description" rows="2"
                          class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">{{ old('description', $menu->description) }}</textarea>
            </div>

            <!-- Foto Menu & Preview -->
            <div>
                <label for="image" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Foto Menu</label>
                <div class="flex items-center gap-4 mb-2">
                    <img id="image-preview" src="{{ $menu->image_url }}" alt="Preview" class="w-16 h-16 rounded-2xl object-contain bg-amber-50 p-2 border">
                    <span class="text-xs text-stone-500">Pilih berkas baru jika ingin mengganti foto menu ini.</span>
                </div>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)"
                       class="w-full px-4 py-2 rounded-xl border border-stone-200 text-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
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
                    Simpan Perubahan
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
