<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-lg tracking-tight">Manajemen Loker</h2>
            <x-ui.button :href="route('admin.loker.create')">+ Loker Baru</x-ui.button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="tableFilter(25, { wilayah: '', status: '' })" x-init="init()">
            <x-ui.card>
                <div class="grid gap-3 sm:grid-cols-5">
                    <x-ui.input type="text" x-model="search" placeholder="Cari judul atau wilayah..." class="sm:col-span-2" />

                    <x-ui.select x-model="filters.wilayah">
                        <option value="">Semua Wilayah</option>
                        @foreach ($wilayahOptions as $w)
                            <option value="{{ $w->nama_wilayah }}">{{ $w->nama_wilayah }}</option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select x-model="filters.status">
                        <option value="">Semua Status</option>
                        <option value="dibuka">Dibuka</option>
                        <option value="ditutup">Ditutup</option>
                    </x-ui.select>

                    <x-ui.button type="button" @click="reset()" variant="outline">Reset</x-ui.button>
                </div>
            </x-ui.card>

            <x-ui.card :padded="false" class="overflow-hidden">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-muted/50">
                        <tr class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <th class="px-4 py-3">Judul</th>
                            <th class="px-4 py-3">Wilayah</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Pelamar</th>
                            <th class="px-4 py-3">Berlaku Sampai</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" x-ref="tbody">
                        @forelse ($lokerList as $loker)
                            <tr class="hover:bg-muted/30" data-row
                                data-search="{{ Str::lower($loker->judul_loker.' '.$loker->wilayah) }}"
                                data-wilayah="{{ $loker->wilayah }}"
                                data-status="{{ $loker->status_loker }}"
                                x-show="isVisible($el)">
                                <td class="px-4 py-3 font-medium">
                                    <a href="{{ route('admin.pelamar.index', ['loker' => $loker->id_loker]) }}" class="hover:underline hover:text-primary">
                                        {{ $loker->judul_loker }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">{{ $loker->wilayah ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="$loker->status_loker === 'dibuka' ? 'success' : 'muted'">
                                        {{ ucfirst($loker->status_loker) }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">{{ $loker->pelamar_count }}</td>
                                <td class="px-4 py-3 text-muted-foreground text-xs">
                                    {{ $loker->end_time ? $loker->end_time->format('d/m') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right space-x-1">
                                    <x-ui.button :href="route('admin.loker.edit', $loker)" variant="ghost" size="sm">Kelola</x-ui.button>
                                    <form method="POST" action="{{ route('admin.loker.destroy', $loker) }}" class="inline" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Hapus loker ini?', destructive: true, confirmText: 'Hapus', form: $el })">
                                        @csrf @method('DELETE')
                                        <x-ui.button type="submit" variant="ghost" size="sm" class="text-destructive hover:text-destructive">Hapus</x-ui.button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-muted-foreground">Belum ada loker.</td></tr>
                        @endforelse
                        @if ($lokerList->isNotEmpty())
                            <tr x-show="total === 0"><td colspan="6" class="px-4 py-8 text-center text-muted-foreground">Tidak ada loker yang cocok dengan filter.</td></tr>
                        @endif
                    </tbody>
                </table>
            </x-ui.card>

            <x-ui.table-filter-footer />
        </div>
    </div>
</x-app-layout>
