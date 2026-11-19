@extends('layouts.auth')

@section('title', 'Masuk ke KantinGO')

@section('content')
<div class="w-full max-w-md">
    <div class="glass rounded-3xl p-6 sm:p-8 shadow-2xl shadow-amber-900/10 border border-white/60">
        <!-- Heading -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-black text-stone-900 tracking-tight">Selamat Datang</h1>
            <p class="text-sm text-stone-600 mt-1">Masuk untuk mengakses layanan kantin kampus</p>
        </div>

        <!-- Flash Alert -->
        @if (session('success'))
            <div class="mb-4 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <!-- Form Login -->
        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="login" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Username atau Email</label>
                <div class="relative">
                    <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                           placeholder="admin / budi_mhs / email..."
                           class="w-full px-4 py-3 rounded-2xl bg-white/80 border border-stone-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 transition-all text-sm outline-none shadow-sm">
                </div>
                @error('login')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-2xl bg-white/80 border border-stone-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 transition-all text-sm outline-none shadow-sm">
                </div>
                @error('password')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-stone-600 select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-stone-300">
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold text-sm shadow-lg shadow-amber-600/25 transition-all transform active:scale-[0.99] flex items-center justify-center gap-2">
                <span>Masuk Sekarang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <!-- Quick Demo Accounts -->
        <div class="mt-6 pt-5 border-t border-stone-200/60">
            <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider text-center mb-2.5">Pilih Akun Demo Cepat</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" onclick="fillCreds('admin', 'KantinGo123!')" class="p-2 rounded-xl bg-amber-50 hover:bg-amber-100/80 border border-amber-200/70 text-amber-900 font-medium text-left transition-colors">
                    <span class="block font-bold text-[11px] text-amber-700">👑 ADMIN</span>
                    <span class="text-[10px] text-stone-600">admin</span>
                </button>
                <button type="button" onclick="fillCreds('seller_viktor', 'password123')" class="p-2 rounded-xl bg-orange-50 hover:bg-orange-100/80 border border-orange-200/70 text-orange-900 font-medium text-left transition-colors">
                    <span class="block font-bold text-[11px] text-orange-700">🏪 SELLER VIKTOR</span>
                    <span class="text-[10px] text-stone-600">seller_viktor</span>
                </button>
                <button type="button" onclick="fillCreds('budi_mhs', 'password123')" class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100/80 border border-blue-200/70 text-blue-900 font-medium text-left transition-colors">
                    <span class="block font-bold text-[11px] text-blue-700">🎓 MAHASISWA</span>
                    <span class="text-[10px] text-stone-600">budi_mhs</span>
                </button>
                <button type="button" onclick="fillCreds('irwan_dosen', 'password123')" class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100/80 border border-emerald-200/70 text-emerald-900 font-medium text-left transition-colors">
                    <span class="block font-bold text-[11px] text-emerald-700">👨‍🏫 DOSEN</span>
                    <span class="text-[10px] text-stone-600">irwan_dosen</span>
                </button>
            </div>
        </div>

        <!-- Footer link -->
        <div class="mt-5 text-center text-xs text-stone-600">
            <span>Belum memiliki akun mahasiswa/dosen?</span>
            <a href="{{ route('register') }}" class="font-bold text-amber-700 hover:text-amber-800 underline ml-1">Daftar Akun Baru</a>
        </div>
    </div>
</div>

<script>
    function fillCreds(u, p) {
        document.getElementById('login').value = u;
        document.getElementById('password').value = p;
    }
</script>
@endsection
