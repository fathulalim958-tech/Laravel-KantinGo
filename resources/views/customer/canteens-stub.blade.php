<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Katalog Kantin · KantinGO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-amber-50/50 p-8 text-stone-800">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow">
        <h1 class="text-2xl font-bold text-amber-900">🎓 Katalog Kantin Universitas Pamulang</h1>
        <p class="text-sm text-stone-600 mt-1">Halo, {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->user_type ?? 'Pelanggan') }})</p>
        <div class="mt-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-rose-600 text-white rounded-xl text-sm font-semibold">Keluar</button>
            </form>
        </div>
    </div>
</body>
</html>
