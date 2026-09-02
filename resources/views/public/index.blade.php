<x-layouts.public :title="'Rekrutmen YPI Al Azhar'">

    <!-- Hero -->
    @php
        $heroImages = collect(range(1, 30))
            ->map(fn ($n) => asset('images/hero-carousel/hero-'.str_pad($n, 2, '0', STR_PAD_LEFT).'.jpg'))
            ->all();
    @endphp
    <section class="relative overflow-hidden" x-data="{ panduanOpen: false }">
        <div
            class="absolute inset-0 -z-10"
            x-data="{
                images: @js($heroImages),
                pos: 0,
                front: 'a',
                srcA: null,
                srcB: null,
                init() {
                    this.srcA = this.images[0];
                    this.srcB = this.images[1 % this.images.length];
                    setInterval(() => {
                        this.pos = (this.pos + 1) % this.images.length;
                        const upcoming = this.images[(this.pos + 1) % this.images.length];
                        if (this.front === 'a') {
                            this.srcB = upcoming;
                            this.front = 'b';
                        } else {
                            this.srcA = upcoming;
                            this.front = 'a';
                        }
                    }, 5000);
                },
            }"
            x-init="init()"
        >
            <img :src="srcA" class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000 ease-in-out" :class="front === 'a' ? 'opacity-100' : 'opacity-0'" alt="Rekrutmen YPI Al Azhar">
            <img :src="srcB" class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000 ease-in-out" :class="front === 'b' ? 'opacity-100' : 'opacity-0'" alt="Rekrutmen YPI Al Azhar">
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/45 to-black/20"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/20 to-transparent"></div>
        </div>

        <div class="w-full px-4 sm:px-6 lg:px-10 xl:px-16 pt-24 pb-28 sm:pt-32 sm:pb-36">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3">
                    <span class="h-px w-8 bg-brand-green-500"></span>
                    <span class="text-xs font-semibold tracking-[0.2em] text-white/90 uppercase">Wujudkan Visi Pendidikan Islam</span>
                </div>

                <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold tracking-tight text-white leading-[1.1]">
                    Membangun Masa Depan
                    <span class="block italic bg-gradient-to-r from-brand-green-500 to-[#8cfa9e] bg-clip-text text-transparent">Generasi Qur'ani</span>
                </h1>

                <p class="mt-5 text-white/85 leading-relaxed max-w-xl">
                    Bergabunglah untuk melanjutkan tradisi keunggulan Al-Azhar. Kami mencari pendidik dan profesional yang visioner guna membina generasi pemimpin masa depan.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <x-ui.button href="#peluang-karir" variant="brand" size="default" class="h-11 rounded-full px-6">
                        Jelajahi Karir
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" /></svg>
                    </x-ui.button>
                    <x-ui.button type="button" variant="outline-invert" size="default" class="h-11 rounded-full px-6" @click="panduanOpen = true">Panduan Seleksi</x-ui.button>
                </div>
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

    <!-- Search card (overlaps hero bottom edge) -->
    <div class="w-full px-4 sm:px-6 lg:px-10 xl:px-16 -mt-14 sm:-mt-16 relative z-10">
        <form
            method="GET"
            action="{{ route('loker.list') }}"
            class="rounded-2xl border bg-background shadow-xl p-4 sm:p-5"
            x-data="{
                query: '',
                open: false,
                jobs: {{ Illuminate\Support\Js::from($lokerUntukPencarian->map(fn ($j) => ['id' => $j->id_loker, 'judul' => $j->judul_loker, 'wilayah' => $j->wilayah])) }},
                get filtered() {
                    if (this.query.trim() === '') return [];
                    const q = this.query.toLowerCase();
                    return this.jobs.filter((j) => j.judul.toLowerCase().includes(q) || (j.wilayah ?? '').toLowerCase().includes(q));
                },
            }"
        >
            <div class="grid gap-4 sm:grid-cols-3 sm:items-end">
                <div class="relative">
                    <label for="hero-q" class="block text-xs font-semibold tracking-wide text-muted-foreground uppercase">Cari Posisi</label>
                    <div class="relative mt-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <x-ui.input
                            type="text" id="hero-q" name="q" placeholder="Contoh: Guru Matematika" class="h-11 pl-9" autocomplete="off"
                            x-model="query"
                            @focus="open = true"
                            @click.outside="open = false"
                        />
                    </div>

                    <div
                        x-show="open && filtered.length > 0"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute z-30 mt-1.5 w-full rounded-lg border bg-popover text-popover-foreground shadow-lg overflow-y-auto"
                        style="max-height: 20rem;"
                    >
                        <template x-for="job in filtered.slice(0, 8)" :key="job.id">
                            <a :href="'/loker/' + job.id" class="flex items-center justify-between gap-2 px-4 py-2.5 text-sm hover:bg-accent border-b last:border-b-0">
                                <span class="font-medium" x-text="job.judul"></span>
                                <span class="text-xs text-muted-foreground shrink-0" x-text="job.wilayah"></span>
                            </a>
                        </template>
                    </div>
                </div>

                <div>
                    <label for="hero-wilayah" class="block text-xs font-semibold tracking-wide text-muted-foreground uppercase">Wilayah</label>
                    <x-ui.select id="hero-wilayah" name="wilayah" class="h-11 mt-1.5">
                        <option value="">Semua Wilayah</option>
                        @foreach ($wilayahOptions as $w)
                            <option value="{{ $w }}">{{ $w }}</option>
                        @endforeach
                    </x-ui.select>
                </div>

                <div class="flex gap-2">
                    <div class="flex-1">
                        <label for="hero-jenjang" class="block text-xs font-semibold tracking-wide text-muted-foreground uppercase">Jenis Posisi</label>
                        <x-ui.select id="hero-jenjang" name="jenjang" class="h-11 mt-1.5">
                            <option value="">Semua Jenis</option>
                            @foreach ($jenjangOptions as $j)
                                <option value="{{ $j->id_jenjang }}">{{ $j->nama_jenjang }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <x-ui.button type="submit" variant="navy" size="default" class="h-11 mt-[1.375rem] rounded-full px-6 shrink-0">Tampilkan</x-ui.button>
                </div>
            </div>
        </form>
    </div>

    <!-- Peluang Karir Terbuka -->
    <section id="peluang-karir" class="bg-[#f6f9fd]">
        <div class="w-full px-4 sm:px-6 lg:px-10 xl:px-16 pt-10 sm:pt-14 pb-16 sm:pb-20">
            <div class="max-w-2xl mx-auto text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-brand-navy-100 bg-white px-3 py-1 text-xs font-semibold text-brand-navy-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-navy-600"></span>
                    Lowongan Terbaru
                </span>
                <h2 class="mt-4 text-2xl sm:text-3xl font-bold tracking-tight">Peluang Karir Terbuka</h2>
                <p class="mt-3 text-sm text-muted-foreground leading-relaxed">
                    Filter berdasarkan unit, jenis posisi, dan kata kunci. Setiap lowongan menampilkan jadwal penting serta ringkasan fokus peran agar Anda bisa memutuskan dengan cepat.
                </p>

                <span class="mt-5 inline-flex items-center gap-1.5 rounded-full border bg-white px-4 py-1.5 text-sm font-medium text-brand-navy-600">
                    {{ $totalLowongan }} Lowongan Aktif &middot; Diperbarui {{ now()->translatedFormat('d M Y') }}
                </span>
            </div>

            @if ($lokerTerbaru->isEmpty())
                <x-ui.card class="mt-10 text-center text-muted-foreground bg-white">
                    Belum ada lowongan yang dibuka saat ini. Silakan cek kembali nanti.
                </x-ui.card>
            @else
                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($lokerTerbaru as $loker)
                        <a href="{{ route('loker.show', $loker) }}" class="block h-full">
                            <x-loker-card :loker="$loker" />
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="mt-10 text-center">
                <x-ui.button :href="route('loker.list')" variant="outline" size="default" class="h-11 rounded-full px-6">Lihat Seluruh Lowongan</x-ui.button>
            </div>
        </div>
    </section>

    <!-- Stats + Why join -->
    <section class="w-full px-4 sm:px-6 lg:px-10 xl:px-16 py-16 sm:py-20">
        <div class="rounded-2xl bg-brand-navy-50 px-6 py-6 sm:px-10 sm:py-8">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center sm:text-left sm:divide-x divide-brand-navy-100">
                @foreach ([
                    ['value' => '40+', 'label' => 'Unit pendidikan'],
                    ['value' => '60+', 'label' => 'Tahun pengabdian'],
                    ['value' => '1.200+', 'label' => 'Tenaga pendidik'],
                    ['value' => 'Nasional', 'label' => 'Jaringan yayasan'],
                ] as $stat)
                    <div class="sm:pl-6 sm:first:pl-0">
                        <p class="text-2xl sm:text-3xl font-bold text-brand-navy-600">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-xs sm:text-sm text-muted-foreground">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-16 sm:mt-20">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Mengapa berkarier bersama kami</h2>
            <p class="mt-2 text-sm text-muted-foreground max-w-2xl">Nilai Islami, standar profesional, dan pengembangan berkelanjutan untuk mendukung pendidikan generasi Qur'ani.</p>

            <div class="mt-8 grid gap-5 sm:grid-cols-3">
                @foreach ([
                    ['title' => 'Visi yang jelas', 'desc' => 'Menjaga tradisi keunggulan Al-Azhar sekaligus mengadopsi praktik pendidikan yang relevan.'],
                    ['title' => 'Pengembangan karier', 'desc' => 'Pelatihan berkala, mentoring akademik, dan jalur promosi yang transparan.'],
                    ['title' => 'Lingkungan bermartabat', 'desc' => 'Kolaborasi antar unit, budaya saling menghormati, dan dukungan kesejahteraan pegawai.'],
                ] as $item)
                    <div class="rounded-xl border bg-background p-6">
                        <span class="block h-1 w-8 rounded-full bg-brand-green-500"></span>
                        <h3 class="mt-4 font-semibold">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-sm text-muted-foreground leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-10">
            <p class="text-xs font-semibold tracking-widest text-brand-navy-600 uppercase">Ringkasan Manfaat</p>
            <div class="mt-4 flex flex-wrap gap-2.5">
                @foreach (['Kompensasi kompetitif', 'BPJS & jaminan wajib', 'Cuti & hari libur nasional', 'Pengembangan profesional', 'Lingkungan Islami'] as $benefit)
                    <span class="rounded-full bg-brand-navy-50 px-4 py-2 text-sm font-medium text-brand-navy-600">{{ $benefit }}</span>
                @endforeach
            </div>
        </div>

        <!-- CTA -->
        <div class="mt-10 rounded-2xl bg-brand-navy-50 p-4 sm:p-6">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                <div class="flex-1 rounded-xl bg-background p-5">
                    <h3 class="font-semibold">Siap memulai karier bersama kami?</h3>
                    <p class="mt-1 text-sm text-muted-foreground">Jelajahi seluruh lowongan yang tersedia dan temukan posisi yang sesuai dengan Anda.</p>
                </div>
                <div class="flex shrink-0 gap-3 lg:pr-2">
                    <x-ui.button :href="route('loker.list')" variant="navy" size="default" class="h-11 rounded-full px-6">Lihat Lowongan</x-ui.button>
                </div>
            </div>
        </div>

        <p class="mt-6 text-sm text-muted-foreground">
            Butuh bantuan teknis lamaran?
            <a href="mailto:karir@al-azhar.or.id" class="font-medium text-brand-navy-600 hover:underline">karir@al-azhar.or.id</a>
            &middot;
            <a href="tel:+622172783683" class="font-medium text-brand-navy-600 hover:underline">(021) 72783683</a>
        </p>
    </section>

    <!-- Alur seleksi -->
    <section class="bg-[#f6f9fd] border-t">
        <div class="w-full px-4 sm:px-6 lg:px-10 xl:px-16 py-16 sm:py-20">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Alur seleksi hingga bergabung</h2>
            <p class="mt-2 text-sm text-muted-foreground max-w-2xl">Enam tahapan utama setelah lamaran masuk — dari verifikasi hingga penyambutan resmi.</p>

            <div class="mt-8 rounded-2xl border bg-background p-6 sm:p-8 overflow-x-auto">
                <div class="flex items-center justify-center gap-2 sm:gap-4 min-w-max mx-auto">
                    @foreach ([
                        ['icon' => 'document', 'label' => 'Aplikasi Masuk'],
                        ['icon' => 'pencil', 'label' => 'Tes Tertulis & Tes Keahlian'],
                        ['icon' => 'chat', 'label' => 'Interview'],
                        ['icon' => 'users', 'label' => 'Orientasi'],
                        ['icon' => 'heart', 'label' => 'Medical Checkup'],
                        ['icon' => 'badge', 'label' => 'Selamat Bergabung'],
                    ] as $i => $step)
                        @if ($i > 0)
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 text-muted-foreground/50 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        @endif
                        <div class="flex flex-col items-center gap-2 w-28 sm:w-32 text-center shrink-0">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-navy-50 text-brand-navy-600">
                                @switch($step['icon'])
                                    @case('document')
                                        {{-- document text: application submitted --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                        @break
                                    @case('pencil')
                                        {{-- pencil square: written test --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                        @break
                                    @case('chat')
                                        {{-- chat bubbles: interview --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155a48.474 48.474 0 0 0 1.905-.164c1.131-.094 1.976-1.057 1.976-2.192v-1.28" /></svg>
                                        @break
                                    @case('users')
                                        {{-- users: orientation --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                                        @break
                                    @case('heart')
                                        {{-- heart: medical checkup --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                                        @break
                                    @case('badge')
                                        {{-- check badge: welcomed aboard --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" /></svg>
                                @endswitch
                            </span>
                            <span class="text-[11px] font-semibold tracking-wide text-foreground uppercase leading-tight">{{ $step['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
