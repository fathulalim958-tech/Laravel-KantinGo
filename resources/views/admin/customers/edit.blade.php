@extends('layouts.admin')

@section('title', 'Edit Pelanggan: ' . $customer->name)
@section('page_title', 'Perbarui Data Pelanggan')
@section('page_subtitle', 'Edit informasi akun, identitas civitas, atau reset sandi pelanggan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-600 hover:text-stone-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Pelanggan
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-sm">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username & Email Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-2">Username <span class="text-rose-500">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username', $customer->username) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition font-mono @error('username') border-rose-500 @enderror">
                    @error('username')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-2">Email <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Jenis Civitas & NIM/NIP Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="user_type" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-2">Jenis Civitas <span class="text-rose-500">*</span></label>
                    <select id="user_type" name="user_type" required
                            class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('user_type') border-rose-500 @enderror">
                        <option value="mahasiswa" {{ old('user_type', $customer->user_type) === 'mahasiswa' ? 'selected' : '' }}>🎓 Mahasiswa</option>
                        <option value="dosen" {{ old('user_type', $customer->user_type) === 'dosen' ? 'selected' : '' }}>👨‍🏫 Dosen</option>
                        <option value="staf" {{ old('user_type', $customer->user_type) === 'staf' ? 'selected' : '' }}>💼 Staf Kampus</option>
                    </select>
                    @error('user_type')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="identifier_number" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-2">NIM / NIP / NIDN</label>
                    <input type="text" id="identifier_number" name="identifier_number" value="{{ old('identifier_number', $customer->identifier_number) }}" placeholder="Contoh: 2021140001"
                           class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition font-mono @error('identifier_number') border-rose-500 @enderror">
                    @error('identifier_number')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- No Telepon -->
            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-2">Nomor Telepon / WhatsApp</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" placeholder="Contoh: 081234567890"
                       class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('phone') border-rose-500 @enderror">
                @error('phone')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reset Password -->
            <div class="pt-4 border-t border-stone-100">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Reset Kata Sandi</label>
                <span class="text-xs text-stone-500 block mb-2">Kosongkan kolom ini jika tidak ingin mengubah kata sandi pelanggan.</span>
                <input type="password" id="password" name="password" placeholder="Minimal 8 karakter baru"
                       class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition @error('password') border-rose-500 @enderror">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Aktif Checkbox -->
            <div class="flex items-center gap-3 pt-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-amber-600 border-stone-300 rounded focus:ring-amber-500">
                <label for="is_active" class="text-sm font-semibold text-stone-700 select-none">
                    Akun Aktif (Dapat login dan melakukan pemesanan makanan)
                </label>
            </div>

            <!-- Actions -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('admin.customers.index') }}" class="px-5 py-2.5 rounded-xl border border-stone-300 text-sm font-semibold text-stone-600 hover:bg-stone-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
