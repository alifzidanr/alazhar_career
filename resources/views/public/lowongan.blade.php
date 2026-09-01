<x-layouts.public :title="'Lowongan Kerja - Rekrutmen YPI Al Azhar'">
    <div class="w-full px-4 sm:px-6 lg:px-10 xl:px-16 py-10"
         x-data="tableFilter(25, { unit: '', jenjang: '', wilayah: '' }, { terbaru: { field: 'start', dir: 'desc' }, terlama: { field: 'start', dir: 'asc' }, batas_terdekat: { field: 'end', dir: 'asc' } })"
         x-init="
            const qs = new URLSearchParams(location.search);
            search = qs.get('q') || '';
            filters.unit = qs.get('unit') || '';
            filters.jenjang = qs.get('jenjang') || '';
            filters.wilayah = qs.get('wilayah') || '';
            init();
         ">
        <h1 class="text-2xl font-semibold tracking-tight mb-1">Seluruh Lowongan</h1>
        <p class="text-muted-foreground mb-8">Bergabunglah bersama YPI Al Azhar. Berikut {{ $lokerList->count() }} lowongan yang sedang dibuka.</p>

        <x-ui.card class="mb-8">
            <div class="grid gap-3">
                <x-ui.input type="text" x-model="search" placeholder="Cari judul atau kata kunci..." />

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <x-ui.select x-model="filters.unit">
                        <option value="">Semua Unit Kerja</option>
                        @foreach ($unitOptions as $u)
                            <option value="{{ $u->id_unit_kerja }}">{{ $u->nama_unit }}</option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select x-model="filters.jenjang">
                        <option value="">Semua Jenis Posisi</option>
                        @foreach ($jenjangOptions as $j)
                            <option value="{{ $j->id_jenjang }}">{{ $j->nama_jenjang }}</option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select x-model="filters.wilayah">
                        <option value="">Semua Wilayah</option>
                        @foreach ($wilayahOptions as $w)
                            <option value="{{ $w }}">{{ $w }}</option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select x-model="sort">
                        <option value="terbaru">Terbaru</option>
                        <option value="terlama">Terlama</option>
                        <option value="batas_terdekat">Batas Lamaran Terdekat</option>
                    </x-ui.select>
                </div>

                <div>
                    <x-ui.button type="button" @click="reset()" variant="outline" class="rounded-full">Reset Filter</x-ui.button>
                </div>
            </div>
        </x-ui.card>

        @if ($lokerList->isEmpty())
            <x-ui.card class="text-center text-muted-foreground">
                Belum ada lowongan yang dibuka saat ini. Silakan cek kembali nanti.
            </x-ui.card>
        @else
            <div x-show="total === 0">
                <x-ui.card class="text-center text-muted-foreground">
                    Tidak ada lowongan yang cocok dengan filter Anda. Coba ubah atau reset filter.
                </x-ui.card>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" x-ref="tbody">
                @foreach ($lokerList as $loker)
                    <a href="{{ route('loker.show', $loker) }}" class="block" data-row
                       data-search="{{ Str::lower($loker->judul_loker.' '.$loker->deskripsi_loker) }}"
                       data-unit="{{ $loker->kriteria->pluck('id_unit_kerja')->filter()->unique()->implode(' ') }}"
                       data-jenjang="{{ $loker->id_jenjang }}"
                       data-wilayah="{{ $loker->wilayah }}"
                       data-start="{{ optional($loker->start_time)->timestamp ?? 0 }}"
                       data-end="{{ optional($loker->end_time)->timestamp ?? PHP_INT_MAX }}"
                       x-show="isVisible($el)">
                        <x-loker-card :loker="$loker" />
                    </a>
                @endforeach
            </div>

            <x-ui.table-filter-footer />
        @endif
    </div>
</x-layouts.public>
