@extends('layouts.kasir')
@section('title','Dashboard Kasir')

@section('body')
  <div class="rounded-[26px] border border-white/10 bg-white/5 backdrop-blur-2xl p-6">
    <div class="text-lg font-semibold">Halo, {{ auth()->user()->name }}</div>
    <div class="mt-1 text-sm text-white/60">Siap buat transaksi hari ini?</div>

    <a href="{{ route('kasir.sales.create') }}"
      class="mt-5 inline-flex items-center justify-center rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85">
      + Mulai Transaksi
    </a>
  </div>
@endsection
