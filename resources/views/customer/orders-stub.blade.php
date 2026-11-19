@extends('layouts.customer')
@section('title', 'Riwayat Pesanan')
@section('content')
<div class="bg-white rounded-3xl p-8 border border-stone-200 text-center max-w-lg mx-auto">
    <div class="text-4xl mb-3">📋</div>
    <h2 class="text-xl font-bold text-stone-900">Riwayat & Pelacakan Pesanan</h2>
    <p class="text-sm text-stone-500 mt-1">Daftar riwayat dan pelacakan live token privat akan diaktifkan pada Fase 9.</p>
    <a href="{{ route('customer.canteens') }}" class="inline-block mt-4 px-4 py-2 bg-amber-600 text-white rounded-xl text-xs font-bold">Kembali ke Katalog Kantin</a>
</div>
@endsection
