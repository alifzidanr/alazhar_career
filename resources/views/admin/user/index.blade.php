<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Manajemen Pengguna</h2>
    </x-slot>

    <div class="py-8" x-data="{ pwModal: { show: false, action: '', nama: '' } }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-ui.card title="Tambah Pengguna" description="Pengguna baru akan dapat masuk ke portal admin menggunakan email dan password ini.">
                <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div>
                        <x-input-label for="nama" value="Nama" />
                        <x-ui.input type="text" id="nama" name="nama" value="{{ old('nama') }}" required class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-ui.input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-ui.input type="password" id="password" name="password" required class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                        <x-ui.input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 w-full" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.button type="submit">Tambah Pengguna</x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            <div x-data="tableFilter(25)" x-init="init()" class="space-y-6">
                <x-ui.card>
                    <div class="flex gap-3">
                        <x-ui.input type="text" x-model="search" placeholder="Cari nama atau email..." class="flex-1" />
                        <x-ui.button type="button" @click="reset()" variant="outline">Reset</x-ui.button>
                    </div>
                </x-ui.card>

                <x-ui.card :padded="false" class="overflow-hidden">
                    <table class="min-w-full divide-y text-sm">
                        <thead class="bg-muted/50">
                            <tr class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-3">Pengguna</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" x-ref="tbody">
                            @forelse ($users as $u)
                                <tr class="hover:bg-muted/30" data-row data-search="{{ Str::lower($u->nama.' '.$u->email) }}" x-show="isVisible($el)">
                                    <td class="px-4 py-3 align-top">
                                        <form method="POST" action="{{ route('admin.users.update', $u) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf @method('PATCH')
                                            <x-ui.input type="text" name="nama" value="{{ $u->nama }}" placeholder="Nama" class="h-8 w-40 text-sm" />
                                            <x-ui.input type="email" name="email" value="{{ $u->email }}" placeholder="Email" class="h-8 w-56 text-sm" />
                                            <x-ui.button type="submit" variant="outline" size="sm">Simpan</x-ui.button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-right align-top">
                                        <div class="flex justify-end gap-1">
                                            <x-ui.button type="button" variant="ghost" size="sm"
                                                @click="pwModal = { show: true, action: '{{ route('admin.users.password', $u) }}', nama: {{ \Illuminate\Support\Js::from($u->nama) }} }">
                                                Ubah Password
                                            </x-ui.button>
                                            @if ($u->id_admin !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Hapus pengguna ' + @js($u->nama) + '?', description: 'Pengguna tidak akan bisa masuk lagi setelah dihapus.', destructive: true, confirmText: 'Hapus', form: $el })">
                                                    @csrf @method('DELETE')
                                                    <x-ui.button type="submit" variant="ghost" size="sm" class="text-destructive hover:text-destructive">Hapus</x-ui.button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-4 py-8 text-center text-muted-foreground">Belum ada pengguna.</td></tr>
                            @endforelse
                            @if ($users->isNotEmpty())
                                <tr x-show="total === 0"><td colspan="2" class="px-4 py-8 text-center text-muted-foreground">Tidak ada pengguna yang cocok.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </x-ui.card>

                <x-ui.table-filter-footer />
            </div>
        </div>

        <!-- Change password modal -->
        <div x-show="pwModal.show" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="pwModal.show = false"></div>
            <div
                x-show="pwModal.show"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="relative w-full max-w-sm rounded-lg border bg-background p-6 shadow-lg"
                @keydown.escape.window="pwModal.show = false"
                x-data="{ password: '', confirmation: '' }"
            >
                <h3 class="text-base font-semibold">Ubah Password</h3>
                <p class="mt-1.5 text-sm text-muted-foreground">Password baru untuk <span x-text="pwModal.nama" class="font-medium text-foreground"></span>. Password lama tidak diperlukan.</p>

                <form
                    method="POST"
                    :action="pwModal.action"
                    class="mt-4 space-y-3"
                    @submit.prevent="
                        if (password.length < 8) { alert('Password minimal 8 karakter.'); return; }
                        if (password !== confirmation) { alert('Konfirmasi password tidak cocok.'); return; }
                        $dispatch('confirm-dialog', { title: 'Ubah password untuk ' + pwModal.nama + '?', confirmText: 'Ubah Password', form: $el });
                    "
                >
                    @csrf @method('PATCH')
                    <div>
                        <x-input-label for="modal_password" value="Password Baru" />
                        <x-ui.input type="password" id="modal_password" x-model="password" name="password" required class="mt-1 w-full" />
                    </div>
                    <div>
                        <x-input-label for="modal_password_confirmation" value="Konfirmasi Password Baru" />
                        <x-ui.input type="password" id="modal_password_confirmation" x-model="confirmation" name="password_confirmation" required class="mt-1 w-full" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <x-ui.button type="button" variant="outline" @click="pwModal.show = false">Batal</x-ui.button>
                        <x-ui.button type="submit">Simpan Password</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
