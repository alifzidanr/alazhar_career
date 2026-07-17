<x-layouts.public :title="'Rekrutmen YPI Al Azhar'">

    <!-- Hero -->
    <section class="border-b bg-gradient-to-b from-muted/60 to-background" x-data="{ panduanOpen: false }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 text-center">
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-foreground">
                Wujudkan Visi Pendidikan Islam
            </h1>
            <p class="mt-2 text-xl sm:text-2xl font-semibold text-foreground/80">
                Membangun Masa Depan Generasi Qur'ani
            </p>
            <p class="mt-6 max-w-2xl mx-auto text-muted-foreground leading-relaxed">
                Bergabunglah untuk melanjutkan tradisi keunggulan Al-Azhar. Kami mencari pendidik dan profesional yang visioner guna membina generasi pemimpin masa depan.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <x-ui.button href="#cari-posisi" size="lg">Jelajahi Karir</x-ui.button>
                <x-ui.button type="button" variant="outline" size="lg" @click="panduanOpen = true">Panduan Seleksi</x-ui.button>
            </div>
        </div>

        <!-- Panduan Seleksi modal -->
        <div x-show="panduanOpen" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="panduanOpen = false"></div>
            <div
                x-show="panduanOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="relative w-full max-w-lg max-h-[85vh] overflow-y-auto rounded-lg border bg-background p-6 shadow-lg text-left"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold">Panduan Seleksi</h3>
                        <p class="mt-1 text-sm text-muted-foreground">Tahapan yang akan dilalui setiap pelamar dalam proses rekrutmen YPI Al Azhar.</p>
                    </div>
                    <button type="button" @click="panduanOpen = false" class="shrink-0 text-muted-foreground hover:text-foreground">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @php
                    $deskripsiTahap = [
                        1 => 'Berkas lamaran (data diri, pendidikan, CV, dan dokumen pendukung) diperiksa kelengkapan dan kesesuaiannya.',
                        2 => 'Pelamar yang lolos seleksi berkas mengikuti tes tulis untuk mengukur kompetensi dasar dan bidang keahlian.',
                        3 => 'Wawancara untuk menggali lebih dalam kompetensi, kepribadian, dan kesesuaian nilai dengan visi Al-Azhar.',
                        4 => 'Masa pengenalan lingkungan kerja, budaya, dan sistem pendidikan di lingkungan YPI Al Azhar.',
                        5 => 'Penugasan sementara untuk menilai kinerja langsung, termasuk pemeriksaan kesehatan, sebelum keputusan akhir diambil.',
                        6 => 'Pelamar yang dinyatakan lulus menerima Surat Keputusan (SK) resmi dari HR.',
                        7 => 'Data pelamar terpilih dimigrasikan ke sistem HRIS sebagai pegawai resmi.',
                    ];
                @endphp

                <ol class="mt-5 space-y-4">
                    @foreach ($tahapRekrutmen as $tahap)
                        <li class="flex gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-semibold">{{ $tahap->id_tahap_rekrutmen }}</span>
                            <div>
                                <p class="text-sm font-medium">{{ $tahap->tahap_rekrutmen }}</p>
                                <p class="text-sm text-muted-foreground">{{ $deskripsiTahap[$tahap->id_tahap_rekrutmen] ?? '' }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>

                <div class="mt-6 flex justify-end">
                    <x-ui.button type="button" variant="outline" @click="panduanOpen = false">Tutup</x-ui.button>
                </div>
            </div>
        </div>
    </section>

    <!-- Cari Posisi -->
    <section id="cari-posisi" class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 scroll-mt-20">
        <h2 class="text-xl font-semibold tracking-tight text-center">Cari Posisi yang Sesuai untuk Anda</h2>
        <p class="mt-1.5 text-sm text-muted-foreground text-center">Telusuri lowongan yang tersedia di lingkungan YPI Al Azhar.</p>

        <div class="mt-6" x-data="{
            query: '',
            open: false,
            jobs: {{ Illuminate\Support\Js::from($lokerUntukPencarian->map(fn ($j) => ['id' => $j->id_loker, 'judul' => $j->judul_loker, 'lokasi' => $j->lokasi])) }},
            get filtered() {
                if (this.query.trim() === '') return this.jobs;
                const q = this.query.toLowerCase();
                return this.jobs.filter((j) => j.judul.toLowerCase().includes(q) || (j.lokasi ?? '').toLowerCase().includes(q));
            },
        }" class="relative">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <x-ui.input
                    type="text"
                    x-model="query"
                    @focus="open = true"
                    @click.outside="open = false"
                    placeholder="Cari posisi, contoh: Guru Bahasa Inggris"
                    class="h-11 pl-9"
                    autocomplete="off"
                />
            </div>

            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute z-30 mt-1.5 w-full rounded-lg border bg-popover text-popover-foreground shadow-lg overflow-y-auto"
                style="max-height: 26rem;"
            >
                <template x-for="job in filtered.slice(0, 50)" :key="job.id">
                    <a :href="'/loker/' + job.id" class="flex items-center justify-between gap-2 px-4 py-2.5 text-sm hover:bg-accent border-b last:border-b-0">
                        <span class="font-medium" x-text="job.judul"></span>
                        <span class="text-xs text-muted-foreground shrink-0" x-text="job.lokasi"></span>
                    </a>
                </template>
                <p x-show="filtered.length === 0" class="px-4 py-3 text-sm text-muted-foreground">Tidak ada posisi yang cocok.</p>
            </div>
        </div>

        <p class="mt-4 text-center text-sm text-muted-foreground">
            <span class="inline-flex items-center gap-1.5 rounded-full border bg-background px-3 py-1">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                {{ $totalLowongan }} Lowongan Tersedia
            </span>
        </p>
    </section>

    <!-- Lowongan Terbaru -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-xl font-semibold tracking-tight">Lowongan Terbaru</h2>
                <p class="mt-1 text-sm text-muted-foreground">Posisi yang baru saja dibuka.</p>
            </div>
        </div>

        @if ($lokerTerbaru->isEmpty())
            <x-ui.card class="text-center text-muted-foreground">
                Belum ada lowongan yang dibuka saat ini. Silakan cek kembali nanti.
            </x-ui.card>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($lokerTerbaru as $loker)
                    <a href="{{ route('loker.show', $loker) }}" class="block">
                        <x-ui.card class="hover:border-foreground/30 hover:shadow-md transition-all h-full">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-semibold text-base">{{ $loker->judul_loker }}</h3>
                                <x-ui.badge variant="success" class="shrink-0">Dibuka</x-ui.badge>
                            </div>
                            @if ($loker->lokasi)
                                <p class="text-sm text-muted-foreground mt-1">📍 {{ $loker->lokasi }}</p>
                            @endif
                            @if ($loker->end_time)
                                <p class="text-xs text-muted-foreground/70 mt-3">Batas lamaran: {{ $loker->end_time->format('d/m/Y') }}</p>
                            @endif
                        </x-ui.card>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-10 text-center">
            <x-ui.button :href="route('loker.list')" variant="outline" size="lg">Lihat Seluruh Lowongan</x-ui.button>
        </div>
    </section>
</x-layouts.public>
