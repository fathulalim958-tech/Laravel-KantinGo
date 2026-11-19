@extends('layouts.auth')

@section('title', 'Daftar Akun Civitas UNPAM')

@section('content')
<div class="w-full max-w-lg">
    <div class="glass rounded-3xl p-6 sm:p-8 shadow-2xl shadow-amber-900/10 border border-white/60">
        <!-- Heading -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-black text-stone-900 tracking-tight">Daftar Akun Baru</h1>
            <p class="text-sm text-stone-600 mt-1">Khusus Mahasiswa, Dosen, dan Staf Universitas Pamulang</p>
        </div>

        <!-- Form Register -->
        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="Contoh: Budi Santoso"
                       class="w-full px-4 py-2.5 rounded-2xl bg-white/80 border border-stone-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 transition-all text-sm outline-none shadow-sm">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                           placeholder="budi_santoso"
                           class="w-full px-4 py-2.5 rounded-2xl bg-white/80 border border-stone-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 transition-all text-sm outline-none shadow-sm">
                    @error('username')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="user_type" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Jenis Civitas</label>
                    <select id="user_type" name="user_type" required
                            class="w-full px-4 py-2.5 rounded-2xl bg-white/80 border border-stone-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 transition-all text-sm outline-none shadow-sm">
                        <option value="mahasiswa" {{ old('user_type') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ old('user_type') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="staf" {{ old('user_type') === 'staf' ? 'selected' : '' }}>Staf Kampus</option>
                    </select>
                    @error('user_type')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Email Kampus / Aktif</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="budi@mhs.unpam.ac.id"
                           class="w-full px-4 py-2.5 rounded-2xl bg-white/80 border border-stone-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 transition-all text-sm outline-none shadow-sm">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="identifier_number" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">NIM / NIP / NIDN (Opsional)</label>
                    <input type="text" id="identifier_number" name="identifier_number" value="{{ old('identifier_number') }}"
                           placeholder="211011400123"
                           class="w-full px-4 py-2.5 rounded-2xl bg-white/80 border border-stone-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 transition-all text-sm outline-none shadow-sm">
                </div>
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Nomor WhatsApp (Untuk Notifikasi)</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                       placeholder="081234567890"
                       class="w-full px-4 py-2.5 rounded-2xl bg-white/80 border border-stone-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 transition-all text-sm outline-none shadow-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Kata Sandi</label>
                    <input type="password" id="password" name="password" required minlength="8"
                           placeholder="Minimal 8 karakter"
                           class="w-full px-4 py-2.5 rounded-2xl bg-white/80 border border-stone-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 transition-all text-sm outline-none shadow-sm">
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Ulangi Sandi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                           placeholder="Konfirmasi sandi"
                           class="w-full px-4 py-2.5 rounded-2xl bg-white/80 border border-stone-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 transition-all text-sm outline-none shadow-sm">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold text-sm shadow-lg shadow-amber-600/25 transition-all transform active:scale-[0.99] flex items-center justify-center gap-2 mt-2">
                <span>Daftar Akun Baru</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <!-- Footer link -->
        <div class="mt-5 text-center text-xs text-stone-600 border-t border-stone-200/60 pt-4">
            <span>Sudah memiliki akun?</span>
            <a href="{{ route('login') }}" class="font-bold text-amber-700 hover:text-amber-800 underline ml-1">Masuk ke Akun Anda</a>
        </div>
    </div>
</div>
@endsection
