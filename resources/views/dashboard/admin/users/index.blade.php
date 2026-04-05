@extends('layouts.admin')
@section('title', 'Kelola User')

@section('body')
  <div class="mx-auto w-full max-w-6xl">
    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-start gap-3">
        <button id="openMobileSidebar" type="button"
          class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
          ☰
        </button>

        <div>
          <div
            class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1 text-xs text-white/70">
            <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
            Admin • User Management
          </div>

          <h1 class="mt-2 text-xl font-semibold">Kelola User</h1>
          <p class="mt-1 text-sm text-white/70">
            Kelola akun <span class="font-semibold text-white/85">Admin / Kasir / Kitchen / Pegawai</span>.
            (Akun <span class="font-semibold text-white/85">guest</span> tidak perlu ditampilkan di sini.)
          </p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('admin.cashiers.create') }}"
          class="inline-flex items-center justify-center rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
          + Buat User
        </a>
      </div>
    </div>

    {{-- FLASH --}}
    @if (session('success'))
      <div class="mt-5 rounded-2xl border border-green-500/25 bg-green-500/10 px-4 py-3 text-sm text-green-100">
        {{ session('success') }}
      </div>
    @endif

    {{-- CONTENT --}}
    <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <div class="text-sm font-semibold">Daftar User</div>
          <div class="mt-1 text-xs text-white/65">
            Total: <span class="font-semibold text-white/85">{{ $users->total() }}</span>
          </div>
        </div>

        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
          {{-- Search (client-side) --}}
          <div class="relative w-full sm:w-[320px]">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-white/45">🔎</span>
            <input id="userSearch" type="text" placeholder="Cari nama / email / role..."
              class="w-full rounded-xl border border-white/20 bg-white/10 py-2.5 pl-10 pr-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40" />
          </div>

          {{-- Filter role --}}
          <select id="roleFilter"
            class="w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2.5 text-sm outline-none focus:border-white/40 sm:w-[170px]">
            <option value="">Semua role</option>
            <option value="admin">Admin</option>
            <option value="kasir">Kasir</option>
            <option value="kitchen">Kitchen</option>
            <option value="pegawai">Pegawai</option>
          </select>
        </div>
      </div>

      {{-- MOBILE CARDS --}}
      <div class="mt-4 space-y-3 sm:hidden" id="userListMobile">
        @forelse($users as $u)
          <div class="user-row rounded-2xl border border-white/15 bg-white/10 p-4"
            data-name="{{ strtolower($u->name) }}"
            data-email="{{ strtolower($u->email) }}"
            data-role="{{ strtolower($u->role) }}">

            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="truncate text-sm font-semibold">{{ $u->name }}</div>
                <div class="truncate text-xs text-white/75">{{ $u->email }}</div>
              </div>

              <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-xs">
                {{ $u->role }}
              </span>
            </div>

            <div class="mt-3 grid grid-cols-1 gap-2 text-xs text-white/75">
              <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2">
                <div class="text-white/60">Dibuat oleh</div>
                <div class="mt-0.5 text-white/90">
                  {{ $u->creator?->name ?? '-' }}
                  <span class="text-white/60">({{ $u->creator?->email ?? '-' }})</span>
                </div>
              </div>

              <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2">
                <div class="text-white/60">Dibuat</div>
                <div class="mt-0.5 text-white/90">
                  {{ $u->created_at?->format('d M Y H:i') }}
                </div>
              </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-2">
              <a href="{{ route('admin.cashiers.edit', $u) }}"
                class="rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-xs font-semibold hover:bg-white/15">
                ✏️ Edit
              </a>

              @if(auth()->id() !== $u->id)
                <form method="POST" action="{{ route('admin.cashiers.destroy', $u) }}"
                  onsubmit="return confirm('Hapus user ini?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                    class="rounded-xl border border-red-500/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100 hover:bg-red-500/15">
                    🗑️ Hapus
                  </button>
                </form>
              @endif
            </div>
          </div>
        @empty
          <div class="rounded-2xl border border-white/15 bg-white/10 p-6 text-center text-sm text-white/70">
            Belum ada user.
          </div>
        @endforelse
      </div>

      {{-- DESKTOP TABLE --}}
      <div class="mt-4 hidden overflow-hidden rounded-2xl border border-white/15 sm:block">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[900px] text-left text-sm" id="userTable">
            <thead class="bg-white/10 text-xs text-white/70">
              <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Dibuat oleh</th>
                <th class="px-4 py-3">Dibuat</th>
                <th class="px-4 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              @forelse($users as $u)
                <tr class="user-row hover:bg-white/5"
                  data-name="{{ strtolower($u->name) }}"
                  data-email="{{ strtolower($u->email) }}"
                  data-role="{{ strtolower($u->role) }}">
                  <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                  <td class="px-4 py-3 text-white/80">{{ $u->email }}</td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-xs">
                      {{ $u->role }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-white/80">
                    {{ $u->creator?->name ?? '-' }}
                    <div class="text-xs text-white/60">{{ $u->creator?->email }}</div>
                  </td>
                  <td class="px-4 py-3 text-xs text-white/70">
                    {{ $u->created_at?->format('d M Y H:i') }}
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-2">
                      <a href="{{ route('admin.cashiers.edit', $u) }}"
                        class="rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-xs font-semibold hover:bg-white/15">
                        Edit
                      </a>

                      @if(auth()->id() !== $u->id)
                        <form method="POST" action="{{ route('admin.cashiers.destroy', $u) }}"
                          onsubmit="return confirm('Hapus user ini?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit"
                            class="rounded-xl border border-red-500/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100 hover:bg-red-500/15">
                            Hapus
                          </button>
                        </form>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-4 py-6 text-center text-white/70">
                    Belum ada user.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- PAGINATION --}}
      <div class="mt-4">
        {{ $users->onEachSide(1)->links() }}
      </div>
    </div>
  </div>

  <script>
    (function () {
      const search = document.getElementById('userSearch');
      const roleFilter = document.getElementById('roleFilter');

      function normalize(v) {
        return (v || '').toString().toLowerCase().trim();
      }

      function applyFilter() {
        const q = normalize(search?.value);
        const role = normalize(roleFilter?.value);

        document.querySelectorAll('.user-row').forEach((row) => {
          const name = normalize(row.getAttribute('data-name'));
          const email = normalize(row.getAttribute('data-email'));
          const r = normalize(row.getAttribute('data-role'));

          const matchText = !q || name.includes(q) || email.includes(q) || r.includes(q);
          const matchRole = !role || r === role;

          row.style.display = (matchText && matchRole) ? '' : 'none';
        });
      }

      if (search) search.addEventListener('input', applyFilter);
      if (roleFilter) roleFilter.addEventListener('change', applyFilter);
    })();
  </script>
@endsection