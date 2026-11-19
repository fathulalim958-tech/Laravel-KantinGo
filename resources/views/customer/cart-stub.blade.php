@extends('layouts.customer')
@section('title', 'Keranjang Belanja')
@section('content')
<div class="bg-white rounded-3xl p-8 border border-stone-200 text-center max-w-lg mx-auto">
    <div class="text-4xl mb-3">🛒</div>
    <h2 class="text-xl font-bold text-stone-900">Keranjang Belanja Multi-Kantin</h2>
    <p class="text-sm text-stone-500 mt-1">Modul transaksi atomik dan checkout akan diaktifkan penuh pada Fase 9.</p>
    <a href="{{ route('customer.canteens') }}" class="inline-block mt-4 px-4 py-2 bg-amber-600 text-white rounded-xl text-xs font-bold">Kembali ke Katalog Kantin</a>
</div>
@endsection
