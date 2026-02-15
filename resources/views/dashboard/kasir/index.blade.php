@extends('layouts.kasir')

@section('title', 'Dashboard Kasir')

@section('content')
  <div class="text-center space-y-4">
    <h1 class="text-3xl font-bold">Ini Dashboard Kasir</h1>
    <p class="text-white/70">Role: {{ auth()->user()->role }} | {{ auth()->user()->email }}</p>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="rounded-xl bg-white/10 border border-white/20 px-4 py-2 hover:bg-white/15">
        Logout
      </button>
    </form>
  </div>
@endsection
