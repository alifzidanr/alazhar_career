<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Manajemen Pelamar</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="tableFilter(15)" x-init="init()">

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.pelamar.index', ['tahap' => 0]) }}"
                   class="relative inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium border transition-colors {{ $tahapAktif === 0 ? 'bg-primary text-primary-foreground border-primary' : 'bg-background text-muted-foreground border-input hover:bg-accent hover:text-accent-foreground' }}">
                    Semua
                    @if ($totalSemua > 0)
                        <span class="absolute -top-1.5 -right-1.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold leading-none text-white ring-2 ring-background">{{ $totalSemua }}</span>
                    @endif
                </a>
                @foreach ($tahapOptions as $t)
                    <a href="{{ route('admin.pelamar.index', ['tahap' => $t->id_tahap_rekrutmen]) }}"
                       class="relative inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium border transition-colors {{ $tahapAktif === $t->id_tahap_rekrutmen ? 'bg-primary text-primary-foreground border-primary' : 'bg-background text-muted-foreground border-input hover:bg-accent hover:text-accent-foreground' }}">
                        {{ $t->tahap_rekrutmen }}
                        @if (($counts[$t->id_tahap_rekrutmen] ?? 0) > 0)
                            <span class="absolute -top-1.5 -right-1.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold leading-none text-white ring-2 ring-background">{{ $counts[$t->id_tahap_rekrutmen] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="flex justify-end gap-2">
                <x-ui.input type="text" x-model="search" placeholder="Cari nama pelamar..." class="w-56" />
                <x-ui.button type="button" @click="reset()" variant="outline">Reset</x-ui.button>
            </div>

            <x-ui.card :padded="false" class="overflow-x-auto">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-muted/50">
                        <tr class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Loker</th>
                            <th class="px-4 py-3">Tahap</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tgl Apply</th>
                            <th class="px-4 py-3">Catatan</th>
                            <th class="px-4 py-3">Aksi Cepat</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" x-ref="tbody">
                        @forelse ($pelamarList as $p)
                            <tr class="hover:bg-muted/30" data-row data-search="{{ Str::lower($p->namaLengkap()) }}" x-show="isVisible($el)">
                                <td class="px-4 py-3 font-medium whitespace-nowrap">{{ $p->namaLengkap() }}</td>
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->loker->judul_loker }}</td>
                                <td class="px-4 py-3 whitespace-nowrap"><x-tahap-badge :tahap="$p->tahapRekrutmen" /></td>
                                <td class="px-4 py-3 whitespace-nowrap"><x-status-badge :status="$p->statusPelamar" /></td>
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->tanggal_apply->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.pelamar.catatan', $p) }}" class="flex items-center gap-1">
                                        @csrf @method('PATCH')
                                        <x-ui.input type="text" name="catatan" value="{{ $p->catatan }}" placeholder="Catatan..." class="!h-7 w-32 text-xs" />
                                        <x-ui.button type="submit" size="sm" variant="outline" title="Simpan Catatan" class="!h-7 !px-2">💾</x-ui.button>
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <form method="POST" action="{{ route('admin.pelamar.status', $p) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Tandai tidak lolos?', form: $el })">
                                            @csrf @method('PATCH')
                                            <x-ui.button type="submit" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::TIDAK_LOLOS }}" size="sm" variant="outline" title="Tandai Tidak Lolos" class="!h-7 !px-2">TL</x-ui.button>
                                        </form>
                                        @if ($p->id_tahap_rekrutmen === \App\Models\TahapRekrutmen::TERIMA_SK)
                                            <form method="POST" action="{{ route('admin.pelamar.status', $p) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Tandai diterima?', form: $el })">
                                                @csrf @method('PATCH')
                                                <x-ui.button type="submit" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::DITERIMA }}" size="sm" variant="outline" title="Tandai Diterima" class="!h-7 !px-2 text-emerald-700 border-emerald-200 bg-emerald-50 hover:bg-emerald-100">TR</x-ui.button>
                                            </form>
                                        @endif
                                        @if ($p->id_tahap_rekrutmen === \App\Models\TahapRekrutmen::MIGRASI_DATA)
                                            <form method="POST" action="{{ route('admin.pelamar.status', $p) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Tandai migrated?', form: $el })">
                                                @csrf @method('PATCH')
                                                <x-ui.button type="submit" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::MIGRATED }}" size="sm" variant="outline" title="Tandai Migrated" class="!h-7 !px-2 text-emerald-700 border-emerald-200 bg-emerald-50 hover:bg-emerald-100">MG</x-ui.button>
                                            </form>
                                        @endif
                                        @if ($p->id_tahap_rekrutmen > \App\Models\TahapRekrutmen::SELEKSI_BERKAS)
                                            <form method="POST" action="{{ route('admin.pelamar.mundur', $p) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Mundurkan ke tahap sebelumnya?', form: $el })">
                                                @csrf @method('PATCH')
                                                <x-ui.button type="submit" size="sm" variant="outline" title="Mundurkan ke Tahap Sebelumnya" class="!h-7 !px-2">&larr;</x-ui.button>
                                            </form>
                                        @endif
                                        @if (! in_array($p->id_status_pelamar, [\App\Models\StatusPelamar::MUNDUR, \App\Models\StatusPelamar::DICADANGKAN], true) && $p->id_tahap_rekrutmen < \App\Models\TahapRekrutmen::MIGRASI_DATA)
                                            <form method="POST" action="{{ route('admin.pelamar.lanjut', $p) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Lanjutkan ke tahap berikutnya?', form: $el })">
                                                @csrf @method('PATCH')
                                                <x-ui.button type="submit" size="sm" variant="outline" title="Lanjutkan ke Tahap Berikutnya" class="!h-7 !px-2">&rarr;</x-ui.button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <x-ui.button :href="route('admin.pelamar.show', $p)" variant="ghost" size="sm">Detail &rarr;</x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-muted-foreground">Belum ada pelamar pada tahap ini.</td></tr>
                        @endforelse
                        @if ($pelamarList->isNotEmpty())
                            <tr x-show="total === 0"><td colspan="8" class="px-4 py-8 text-center text-muted-foreground">Tidak ada pelamar yang cocok dengan pencarian.</td></tr>
                        @endif
                    </tbody>
                </table>
            </x-ui.card>

            <x-ui.table-filter-footer />
        </div>
    </div>
</x-app-layout>
