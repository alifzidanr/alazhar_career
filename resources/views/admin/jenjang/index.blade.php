<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Manajemen Jenjang</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            <x-ui.card title="Tambah Jenjang" description="Jenjang ini akan tersedia sebagai pilihan dropdown saat membuat/mengubah loker.">
                <form method="POST" action="{{ route('admin.jenjang.store') }}" class="flex gap-2">
                    @csrf
                    <x-ui.input type="text" name="nama_jenjang" placeholder="cth. Guru SD" value="{{ old('nama_jenjang') }}" required class="flex-1" />
                    <x-ui.button type="submit">Tambah</x-ui.button>
                </form>
                <x-input-error :messages="$errors->get('nama_jenjang')" class="mt-2" />
            </x-ui.card>

            <div x-data="tableFilter(25)" x-init="init()" class="space-y-6">
                <x-ui.card>
                    <div class="flex gap-3">
                        <x-ui.input type="text" x-model="search" placeholder="Cari jenjang..." class="flex-1" />
                        <x-ui.button type="button" @click="reset()" variant="outline">Reset</x-ui.button>
                    </div>
                </x-ui.card>

                <x-ui.card :padded="false" class="overflow-hidden">
                    <table class="min-w-full divide-y text-sm">
                        <thead class="bg-muted/50">
                            <tr class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-3">Jenjang</th>
                                <th class="px-4 py-3">Dipakai</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" x-ref="tbody">
                            @forelse ($jenjangList as $j)
                                <tr class="hover:bg-muted/30" data-row data-search="{{ Str::lower($j->nama_jenjang) }}" x-show="isVisible($el)">
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.jenjang.update', $j) }}" class="flex items-center gap-2">
                                            @csrf @method('PATCH')
                                            <x-ui.input type="text" name="nama_jenjang" value="{{ $j->nama_jenjang }}" class="h-8 text-sm" />
                                            <x-ui.button type="submit" variant="outline" size="sm">Simpan</x-ui.button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-muted-foreground">{{ $j->loker_count }}x</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('admin.jenjang.destroy', $j) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Hapus jenjang ini?', destructive: true, confirmText: 'Hapus', form: $el })">
                                            @csrf @method('DELETE')
                                            <x-ui.button type="submit" variant="ghost" size="sm" class="text-destructive hover:text-destructive">Hapus</x-ui.button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-muted-foreground">Belum ada jenjang.</td></tr>
                            @endforelse
                            @if ($jenjangList->isNotEmpty())
                                <tr x-show="total === 0"><td colspan="3" class="px-4 py-8 text-center text-muted-foreground">Tidak ada jenjang yang cocok.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </x-ui.card>

                <x-ui.table-filter-footer />
            </div>
        </div>
    </div>
</x-app-layout>
