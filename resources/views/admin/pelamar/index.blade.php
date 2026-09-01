<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Manajemen Pelamar</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="tableFilter(25, {}, { '': null, ipk_asc: { field: 'ipk_s1', dir: 'asc' }, ipk_desc: { field: 'ipk_s1', dir: 'desc' } })" x-init="init()">

            @if ($lokerAktifModel)
                <x-ui.card>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm">
                            Menampilkan pelamar untuk loker: <span class="font-semibold">{{ $lokerAktifModel->judul_loker }}</span>
                        </p>
                        <a href="{{ route('admin.pelamar.index', ['tahap' => $tahapAktif, 'kategori' => $kategoriAktif]) }}" class="text-sm font-medium text-primary hover:underline">
                            Lihat semua loker &times;
                        </a>
                    </div>
                </x-ui.card>
            @endif

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.pelamar.index', ['tahap' => 0, 'loker' => $lokerAktif, 'kategori' => $kategoriAktif]) }}"
                   class="relative inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium border transition-colors {{ $tahapAktif === 0 ? 'bg-primary text-primary-foreground border-primary' : 'bg-background text-muted-foreground border-input hover:bg-accent hover:text-accent-foreground' }}">
                    Semua
                    @if ($totalSemua > 0)
                        <span class="absolute -top-1.5 -right-1.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold leading-none text-white ring-2 ring-background">{{ $totalSemua }}</span>
                    @endif
                </a>
                @foreach ($tahapOptions as $t)
                    <a href="{{ route('admin.pelamar.index', ['tahap' => $t->id_tahap_rekrutmen, 'loker' => $lokerAktif, 'kategori' => $kategoriAktif]) }}"
                       class="relative inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium border transition-colors {{ $tahapAktif === $t->id_tahap_rekrutmen ? 'bg-primary text-primary-foreground border-primary' : 'bg-background text-muted-foreground border-input hover:bg-accent hover:text-accent-foreground' }}">
                        {{ $t->tahap_rekrutmen }}
                        @if (($counts[$t->id_tahap_rekrutmen] ?? 0) > 0)
                            <span class="absolute -top-1.5 -right-1.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold leading-none text-white ring-2 ring-background">{{ $counts[$t->id_tahap_rekrutmen] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach (['' => 'Semua Kategori', 'Perguruan Tinggi Negeri' => 'PTN', 'Perguruan Tinggi Swasta' => 'PTS', 'Lain-lain' => 'Lain-lain'] as $val => $label)
                    <a href="{{ route('admin.pelamar.index', ['tahap' => $tahapAktif, 'loker' => $lokerAktif, 'kategori' => $val]) }}"
                       class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium border transition-colors {{ $kategoriAktif === $val ? 'bg-primary text-primary-foreground border-primary' : 'bg-background text-muted-foreground border-input hover:bg-accent hover:text-accent-foreground' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="flex justify-end gap-2">
                <x-ui.input type="text" x-model="search" placeholder="Cari nama pelamar..." class="w-56" />
                <x-ui.button type="button" @click="reset()" variant="outline">Reset</x-ui.button>
            </div>

            <div x-show="selectedIds.length > 0" x-cloak class="flex flex-wrap items-center gap-2 rounded-md border bg-muted/30 px-4 py-2.5">
                <span class="text-sm font-medium mr-1" x-text="`${selectedIds.length} pelamar dipilih`"></span>

                <form method="POST" action="{{ route('admin.pelamar.bulk-status') }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: `Tandai ${selectedIds.length} pelamar tidak lolos?`, destructive: true, form: $el })">
                    @csrf
                    <input type="hidden" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::TIDAK_LOLOS }}">
                    <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                    <x-ui.button type="submit" size="sm" variant="outline">Tandai Tidak Lolos</x-ui.button>
                </form>

                <form method="POST" action="{{ route('admin.pelamar.bulk-status') }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: `Tandai ${selectedIds.length} pelamar diterima?`, form: $el })">
                    @csrf
                    <input type="hidden" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::DITERIMA }}">
                    <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                    <x-ui.button type="submit" size="sm" variant="outline" class="text-emerald-700 border-emerald-200 bg-emerald-50 hover:bg-emerald-100">Tandai Diterima</x-ui.button>
                </form>

                <form method="POST" action="{{ route('admin.pelamar.bulk-status') }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: `Tandai ${selectedIds.length} pelamar migrated?`, form: $el })">
                    @csrf
                    <input type="hidden" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::MIGRATED }}">
                    <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                    <x-ui.button type="submit" size="sm" variant="outline" class="text-emerald-700 border-emerald-200 bg-emerald-50 hover:bg-emerald-100">Tandai Migrated</x-ui.button>
                </form>

                <form method="POST" action="{{ route('admin.pelamar.bulk-mundur') }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: `Mundurkan ${selectedIds.length} pelamar ke tahap sebelumnya?`, form: $el })">
                    @csrf @method('PATCH')
                    <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                    <x-ui.button type="submit" size="sm" variant="outline">&larr; Mundurkan</x-ui.button>
                </form>

                <form method="POST" action="{{ route('admin.pelamar.bulk-lanjut') }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: `Lanjutkan ${selectedIds.length} pelamar ke tahap berikutnya?`, form: $el })">
                    @csrf @method('PATCH')
                    <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                    <x-ui.button type="submit" size="sm" variant="outline">Lanjutkan &rarr;</x-ui.button>
                </form>

                <x-ui.button type="button" size="sm" variant="ghost" @click="selectedIds = []">Batal Pilih</x-ui.button>
            </div>

            <x-ui.card :padded="false" class="overflow-x-auto">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-muted/50">
                        <tr class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <th class="px-4 py-3 w-8">
                                <input type="checkbox" class="rounded border-input" :checked="allVisibleSelected" @change="toggleSelectAll($event.target.checked)" title="Pilih semua yang tampil">
                            </th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Loker</th>
                            <th class="px-4 py-3">Nama Institusi</th>
                            <th class="px-4 py-3">Jurusan</th>
                            <th class="px-4 py-3 cursor-pointer select-none whitespace-nowrap" @click="sort = sort === 'ipk_asc' ? 'ipk_desc' : (sort === 'ipk_desc' ? '' : 'ipk_asc')">
                                IPK
                                <span x-show="sort === 'ipk_asc'">&uarr;</span>
                                <span x-show="sort === 'ipk_desc'">&darr;</span>
                            </th>
                            <th class="px-4 py-3">Tahap</th>
                            @if ($tesTulisAktif)
                                <th class="px-4 py-3">Agama Umum</th>
                                <th class="px-4 py-3">Bidang Studi</th>
                                <th class="px-4 py-3">Inggris Umum</th>
                                <th class="px-4 py-3">Rata-rata</th>
                            @endif
                            @if ($wawancaraAktif)
                                <th class="px-4 py-3">Rata-rata Tes Tulis</th>
                                <th class="px-4 py-3">Wawancara Agama</th>
                                <th class="px-4 py-3">Praktik/Micro Teaching</th>
                                <th class="px-4 py-3">Wawancara Umum</th>
                                <th class="px-4 py-3">Rata-rata Wawancara</th>
                                <th class="px-4 py-3">Nilai Akhir</th>
                            @endif
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tgl Apply</th>
                            <th class="px-4 py-3">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" x-ref="tbody">
                        @forelse ($pelamarList as $p)
                            <tr class="hover:bg-muted/30" data-row data-id="{{ $p->id_pelamar }}" data-search="{{ Str::lower($p->namaLengkap()) }}" data-ipk_s1="{{ $p->ipk_s1 ?? '' }}" x-show="isVisible($el)">
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="rounded border-input" :checked="isSelected({{ $p->id_pelamar }})" @change="toggleSelect({{ $p->id_pelamar }}, $event.target.checked)">
                                </td>
                                <td class="px-4 py-3 font-medium whitespace-nowrap">
                                    <a href="{{ route('admin.pelamar.show', $p) }}" class="hover:underline hover:text-primary">{{ $p->namaLengkap() }}</a>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->loker->judul_loker }}</td>
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->institusi_s1 ?? '-' }}</td>
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->program_studi_s1 ?? '-' }}</td>
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->ipk_s1 ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap"><x-tahap-badge :tahap="$p->tahapRekrutmen" /></td>
                                @if ($tesTulisAktif)
                                    <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->tesTulis?->nilai_tes_agama_umum ?? '-' }}</td>
                                    <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->tesTulis?->nilai_tes_bidang_studi ?? '-' }}</td>
                                    <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->tesTulis?->nilai_tes_inggris_umum ?? '-' }}</td>
                                    <td class="px-4 py-3 font-medium whitespace-nowrap">{{ $p->tesTulis?->nilaiRataRata() ?? '-' }}</td>
                                @endif
                                @if ($wawancaraAktif)
                                    <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->tesTulis?->nilaiRataRata() ?? '-' }}</td>
                                    <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->wawancara?->nilai_wawancara_agama ?? '-' }}</td>
                                    <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->wawancara?->nilai_praktik_micro_teaching ?? '-' }}</td>
                                    <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->wawancara?->nilai_wawancara_umum ?? '-' }}</td>
                                    <td class="px-4 py-3 font-medium whitespace-nowrap">{{ $p->wawancara?->nilaiRataRataWawancara() ?? '-' }}</td>
                                    <td class="px-4 py-3 font-semibold whitespace-nowrap">{{ $p->nilaiAkhir() ?? '-' }}</td>
                                @endif
                                <td class="px-4 py-3 whitespace-nowrap"><x-status-badge :status="$p->statusPelamar" /></td>
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->tanggal_apply->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <form method="POST" action="{{ route('admin.pelamar.status', $p) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Tandai tidak lolos?', form: $el })">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::TIDAK_LOLOS }}">
                                            <x-ui.button type="submit" size="sm" variant="outline" title="Tandai Tidak Lolos" class="!h-7 !px-2">TL</x-ui.button>
                                        </form>
                                        @if ($p->id_tahap_rekrutmen === \App\Models\TahapRekrutmen::TERIMA_SK)
                                            <form method="POST" action="{{ route('admin.pelamar.status', $p) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Tandai diterima?', form: $el })">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::DITERIMA }}">
                                                <x-ui.button type="submit" size="sm" variant="outline" title="Tandai Diterima" class="!h-7 !px-2 text-emerald-700 border-emerald-200 bg-emerald-50 hover:bg-emerald-100">TR</x-ui.button>
                                            </form>
                                        @endif
                                        @if ($p->id_tahap_rekrutmen === \App\Models\TahapRekrutmen::MIGRASI_DATA)
                                            <form method="POST" action="{{ route('admin.pelamar.status', $p) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Tandai migrated?', form: $el })">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::MIGRATED }}">
                                                <x-ui.button type="submit" size="sm" variant="outline" title="Tandai Migrated" class="!h-7 !px-2 text-emerald-700 border-emerald-200 bg-emerald-50 hover:bg-emerald-100">MG</x-ui.button>
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
                            </tr>
                        @empty
                            <tr><td colspan="{{ 10 + ($tesTulisAktif ? 4 : 0) + ($wawancaraAktif ? 6 : 0) }}" class="px-4 py-8 text-center text-muted-foreground">Belum ada pelamar pada tahap ini.</td></tr>
                        @endforelse
                        @if ($pelamarList->isNotEmpty())
                            <tr x-show="total === 0"><td colspan="{{ 10 + ($tesTulisAktif ? 4 : 0) + ($wawancaraAktif ? 6 : 0) }}" class="px-4 py-8 text-center text-muted-foreground">Tidak ada pelamar yang cocok dengan pencarian.</td></tr>
                        @endif
                    </tbody>
                </table>
            </x-ui.card>

            <x-ui.table-filter-footer />
        </div>
    </div>
</x-app-layout>
