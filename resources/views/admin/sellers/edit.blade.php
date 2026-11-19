@extends('layouts.admin')

@section('title', 'Edit Penjual: ' . $seller->name)
@section('page_title', 'Perbarui Akun Penjual')
@section('page_subtitle', 'Edit data akun pengelola kantin')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.sellers.update', $seller) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Nama Lengkap Penjual <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $seller->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Username <span class="text-rose-500">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username', $seller->username) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">
                    @error('username')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Email Penjual <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $seller->email) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Password Baru (Opsional) -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Ganti Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" id="password" name="password" minlength="8"
                           placeholder="Minimal 8 karakter baru"
                           class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">No. Telepon / WA</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $seller->phone) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm">
                </div>
            </div>

            <!-- Status Aktif -->
            <div>
                <label class="flex items-center gap-2 cursor-pointer mt-1">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $seller->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
                    <span class="text-sm font-semibold text-stone-700">Akun Aktif & Diizinkan Mengakses Dashboard</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-stone-100">
                <a href="{{ route('admin.sellers.index') }}" class="px-5 py-2.5 text-stone-600 hover:bg-stone-100 rounded-xl text-sm font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold shadow-md shadow-amber-600/20 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
