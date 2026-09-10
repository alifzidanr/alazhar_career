<x-app-layout>
    <x-slot name="title">Manajemen Pelamar</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Manajemen Pelamar</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6" x-data="tableFilter(25, {}, {
                '': null,
                ipk_asc: { field: 'ipk_s1', dir: 'asc' },
                ipk_desc: { field: 'ipk_s1', dir: 'desc' },
                institusi_asc: { field: 'institusi_s1', dir: 'asc', type: 'string' },
                institusi_desc: { field: 'institusi_s1', dir: 'desc', type: 'string' },
                loker_asc: { field: 'loker', dir: 'asc', type: 'string' },
                loker_desc: { field: 'loker', dir: 'desc', type: 'string' },
            })" x-init="init()">

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
                <x-ui.button :href="route('admin.pelamar.export', ['tahap' => $tahapAktif, 'loker' => $lokerAktif, 'kategori' => $kategoriAktif])" variant="outline">
                    Export XLS
                </x-ui.button>
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

                <div x-data="{
                        emailOpen: false,
                        sending: false,
                        template: '',
                        subject: '',
                        body: '',
                        templates: @js(\App\Support\NotifikasiTemplates::all()),
                        applyTemplate() {
                            if (this.template && this.templates[this.template]) {
                                this.subject = this.templates[this.template].subject;
                                this.body = this.templates[this.template].body;
                            } else {
                                this.subject = '';
                                this.body = '';
                            }
                        },
                    }" class="contents">
                    <x-ui.button type="button" size="sm" variant="outline" @click="emailOpen = true; applyTemplate()">Kirim Email</x-ui.button>

                    <div x-show="emailOpen" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-black/50" @click="if (! sending) emailOpen = false"></div>
                        <div
                            x-show="emailOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="relative w-full max-w-2xl rounded-lg border bg-background p-6 shadow-lg"
                            @keydown.escape.window="if (! sending) emailOpen = false"
                        >
                            <!-- Sending overlay -->
                            <div x-show="sending" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-3 rounded-lg bg-background/90 backdrop-blur-sm">
                                <svg class="h-8 w-8 animate-spin text-primary" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z"></path>
                                </svg>
                                <p class="text-sm font-medium">Mengirim email ke <span x-text="selectedIds.length"></span> pelamar&hellip;</p>
                                <p class="text-xs text-muted-foreground">Mohon tunggu, jangan tutup atau muat ulang halaman ini.</p>
                            </div>

                            <h3 class="text-base font-semibold">Kirim Email ke <span x-text="selectedIds.length"></span> Pelamar</h3>
                            <p class="mt-1 text-xs text-muted-foreground">Gunakan <code>:nama</code>, <code>:loker</code>, dan <code>:tahap</code> pada subjek/isi pesan &mdash; otomatis diganti sesuai data masing-masing pelamar saat dikirim.</p>

                            <div class="mt-4 space-y-3">
                                <div>
                                    <x-ui.label for="bulk-notify-template">Template Pesan</x-ui.label>
                                    <x-ui.select id="bulk-notify-template" x-model="template" @change="applyTemplate()" x-bind:disabled="sending">
                                        <option value="">-- Kosong / Tulis Manual --</option>
                                        @foreach (\App\Support\NotifikasiTemplates::all() as $key => $t)
                                            <option value="{{ $key }}">{{ $t['label'] }}</option>
                                        @endforeach
                                    </x-ui.select>
                                </div>
                                <div>
                                    <x-ui.label for="bulk-notify-subject">Subjek (email)</x-ui.label>
                                    <x-ui.input id="bulk-notify-subject" type="text" x-model="subject" x-bind:disabled="sending" />
                                </div>
                                <div>
                                    <x-ui.label for="bulk-notify-body">Isi Pesan (preview)</x-ui.label>
                                    <x-ui.textarea id="bulk-notify-body" x-model="body" rows="8" x-bind:disabled="sending"></x-ui.textarea>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.pelamar.bulk-notify') }}" @submit.prevent="$dispatch('confirm-dialog', { title: `Kirim email ke ${selectedIds.length} pelamar?`, form: $el })" @confirmed-submit="sending = true" class="mt-5 flex justify-end gap-2">
                                @csrf
                                <input type="hidden" name="channel" value="email">
                                <input type="hidden" name="template" :value="template">
                                <input type="hidden" name="subject" :value="subject">
                                <input type="hidden" name="body" :value="body">
                                <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                                <x-ui.button type="button" variant="outline" @click="emailOpen = false" x-bind:disabled="sending">Batal</x-ui.button>
                                <x-ui.button type="submit" class="bg-blue-600 text-white shadow-sm hover:bg-blue-500 inline-flex items-center gap-2" x-bind:disabled="! subject || ! body || sending">
                                    <svg x-show="sending" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z"></path>
                                    </svg>
                                    <span x-text="sending ? 'Mengirim...' : 'Kirim'"></span>
                                </x-ui.button>
                            </form>
                        </div>
                    </div>
                </div>

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
                            <th class="px-4 py-3 cursor-pointer select-none whitespace-nowrap" @click="sort = sort === 'loker_asc' ? 'loker_desc' : (sort === 'loker_desc' ? '' : 'loker_asc')">
                                Loker
                                <span x-show="sort === 'loker_asc'">&uarr;</span>
                                <span x-show="sort === 'loker_desc'">&darr;</span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none whitespace-nowrap" @click="sort = sort === 'institusi_asc' ? 'institusi_desc' : (sort === 'institusi_desc' ? '' : 'institusi_asc')">
                                Nama Institusi
                                <span x-show="sort === 'institusi_asc'">&uarr;</span>
                                <span x-show="sort === 'institusi_desc'">&darr;</span>
                            </th>
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
                            <tr class="hover:bg-muted/30" data-row data-id="{{ $p->id_pelamar }}" data-search="{{ Str::lower($p->namaLengkap()) }}" data-ipk_s1="{{ $p->ipk_s1 ?? '' }}" data-institusi_s1="{{ Str::lower($p->institusi_s1 ?? '') }}" data-loker="{{ Str::lower($p->loker->judul_loker ?? '') }}" x-show="isVisible($el)">
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="rounded border-input" :checked="isSelected({{ $p->id_pelamar }})" @change="toggleSelect({{ $p->id_pelamar }}, $event.target.checked)">
                                </td>
                                <td class="px-4 py-3 font-medium whitespace-nowrap">
                                    @php
                                        $riwayatOrientasiMundur = $p->riwayatOrientasiMundur;
                                    @endphp
                                    <span class="relative inline-block {{ $riwayatOrientasiMundur ? 'group' : '' }}">
                                        <a href="{{ route('admin.pelamar.show', $p) }}"
                                            class="hover:underline {{ $p->pernahOrientasi ? 'text-red-600 hover:text-red-700' : 'hover:text-primary' }}"
                                        >{{ $p->namaLengkap() }}</a>

                                        @if ($riwayatOrientasiMundur)
                                            <div class="pointer-events-none absolute top-full left-0 z-50 mt-1.5 w-64 scale-95 whitespace-normal rounded-md border bg-popover px-3 py-1.5 text-xs text-popover-foreground opacity-0 shadow-md transition-all duration-150 group-hover:scale-100 group-hover:opacity-100">
                                                <p>Pernah orientasi di unit {{ $riwayatOrientasiMundur['unit'] }}, pada {{ $riwayatOrientasiMundur['tanggal'] }}</p>
                                            </div>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">
                                    <a href="{{ route('admin.pelamar.index', ['loker' => $p->id_loker, 'tahap' => $tahapAktif, 'kategori' => $kategoriAktif]) }}" class="hover:underline hover:text-primary">{{ $p->loker->judul_loker }}</a>
                                </td>
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
