@extends('layouts.admin')
@section('title', 'Edit User')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
        ☰
      </button>
      <div>
        <h1 class="text-xl font-semibold">Edit User</h1>
        <p class="text-sm text-white/70">Ubah nama, email, role, dan (opsional) password.</p>
      </div>
    </div>

    <a href="{{ route('admin.cashiers.index') }}"
      class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
      ← Kembali
    </a>
  </div>

  <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.2fr_0.8fr]">
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
      <form method="POST" action="{{ route('admin.cashiers.update', $user) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
          <label class="text-sm text-white/80">Nama</label>
          <input name="name" value="{{ old('name', $user->name) }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
            placeholder="Nama" />
          @error('name') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="text-sm text-white/80">Email</label>
          <input name="email" type="email" value="{{ old('email', $user->email) }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
            placeholder="email@example.com" />
          @error('email') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="text-sm text-white/80">Role</label>
          <select name="role"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir</option>
            <option value="kitchen" {{ old('role', $user->role) === 'kitchen' ? 'selected' : '' }}>Kitchen</option>
            <option value="pegawai" {{ old('role', $user->role) === 'pegawai' ? 'selected' : '' }}>Pegawai</option>
          </select>
          @error('role') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
        </div>

        <div class="mt-2 rounded-2xl border border-white/15 bg-white/5 p-4">
          <div class="text-sm font-semibold">Ganti Password (opsional)</div>
          <div class="mt-1 text-xs text-white/70">Kosongkan jika tidak ingin mengubah password.</div>

          <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
              <label class="text-sm text-white/80">Password baru</label>
              <input name="password" type="password"
                class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
                placeholder="Minimal 6 karakter" />
              @error('password') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="text-sm text-white/80">Ulangi password baru</label>
              <input name="password_confirmation" type="password"
                class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
                placeholder="Ketik ulang password" />
            </div>
          </div>
        </div>

        <button
          class="w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
          Simpan Perubahan
        </button>
      </form>
    </div>

    <div class="space-y-5">
      <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
        <div class="text-sm font-semibold">Info User</div>
        <div class="mt-3 rounded-2xl border border-white/15 bg-white/10 p-4">
          <div class="text-sm font-semibold">{{ $user->name }}</div>
          <div class="text-xs text-white/70">{{ $user->email }}</div>
          <div class="mt-3 text-xs text-white/70">
            Dibuat oleh:
            <span class="font-semibold text-white/90">{{ $user->creator?->name ?? '-' }}</span>
            <span class="text-white/60">({{ $user->creator?->email ?? '-' }})</span>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection