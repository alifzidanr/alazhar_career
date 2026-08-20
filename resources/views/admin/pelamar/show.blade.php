<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-lg tracking-tight">{{ $pelamar->namaLengkap() }}</h2>
            <x-ui.button :href="url()->previous()" variant="ghost" size="sm">&larr; Kembali</x-ui.button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-ui.card class="flex flex-wrap items-center gap-3">
                <x-tahap-badge :tahap="$pelamar->tahapRekrutmen" />
                <x-status-badge :status="$pelamar->statusPelamar" />
                <span class="text-sm text-muted-foreground">Melamar untuk <strong class="text-foreground">{{ $pelamar->loker->judul_loker }}</strong> pada {{ $pelamar->tanggal_apply->translatedFormat('d M Y') }}</span>
            </x-ui.card>

            @if ($pelamar->cadanganDari)
                <x-ui.alert variant="warning">
                    Pelamar ini adalah <strong>kandidat cadangan</strong> untuk
                    <a href="{{ route('admin.pelamar.show', $pelamar->cadanganDari) }}" class="underline font-medium">{{ $pelamar->cadanganDari->namaLengkap() }}</a>.
                </x-ui.alert>
            @endif

            @if (in_array($pelamar->id_status_pelamar, [\App\Models\StatusPelamar::TIDAK_LOLOS, \App\Models\StatusPelamar::MUNDUR]) && $pelamar->kandidatCadangan->isNotEmpty())
                <x-ui.alert variant="destructive">
                    <p class="font-medium mb-2">Kandidat ini keluar dari proses rekrutmen. Hubungi kandidat cadangan berikut untuk mengisi posisi ini:</p>
                    <ul class="space-y-1">
                        @foreach ($pelamar->kandidatCadangan as $cadangan)
                            <li>
                                <a href="{{ route('admin.pelamar.show', $cadangan) }}" class="underline font-medium">{{ $cadangan->namaLengkap() }}</a>
                                <span>&mdash; <x-status-badge :status="$cadangan->statusPelamar" /></span>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <div class="space-y-6">

                <div class="grid md:grid-cols-2 gap-6">
                    <x-ui.card title="Data Pelamar">
                        <dl class="space-y-4 text-sm">
                            <div><dt class="text-muted-foreground">Nama</dt><dd class="font-medium">{{ $pelamar->namaLengkap() }}</dd></div>
                            <div><dt class="text-muted-foreground">NIK</dt><dd class="font-medium">{{ $pelamar->nik ?: '-' }}</dd></div>
                            <div><dt class="text-muted-foreground">Tanggal Apply</dt><dd class="font-medium">{{ $pelamar->tanggal_apply->translatedFormat('d M Y') }}</dd></div>
                            <div><dt class="text-muted-foreground">Tanggal Lahir</dt><dd class="font-medium">{{ $pelamar->tanggal_lahir->translatedFormat('d M Y') }} ({{ $pelamar->usia() }} tahun)</dd></div>
                            <div><dt class="text-muted-foreground">Jenis Kelamin</dt><dd class="font-medium">{{ $pelamar->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
                            <div><dt class="text-muted-foreground">Email</dt><dd class="font-medium">{{ $pelamar->email ?: '-' }}</dd></div>
                            <div><dt class="text-muted-foreground">No. WhatsApp</dt><dd class="font-medium">{{ $pelamar->no_hp ?: '-' }}</dd></div>

                            <div><dt class="text-muted-foreground">Alamat</dt><dd class="font-medium">{{ $pelamar->alamat }}</dd></div>
                            <div><dt class="text-muted-foreground">Pernah Mengikuti Rekrutmen Sebelumnya</dt><dd class="font-medium">{{ $pelamar->pernah_rekrutmen_sebelumnya ?: '-' }}</dd></div>
                            @if ($pelamar->pernah_rekrutmen_sebelumnya === 'Ya')
                                <div>
                                    <dt class="text-muted-foreground">Kapan &amp; Sampai Tahap</dt>
                                    <dd class="font-medium">{{ $pelamar->bulanRekrutmenSebelumnyaLabel() }} {{ $pelamar->tahun_rekrutmen_sebelumnya }} &mdash; {{ $pelamar->tahapRekrutmenSebelumnya->tahap_rekrutmen ?? '-' }}</dd>
                                </div>
                            @endif
                            <div><dt class="text-muted-foreground">Pernah Bekerja di Al Azhar</dt><dd class="font-medium">{{ $pelamar->pernah_bekerja_di_al_azhar ?: '-' }}</dd></div>
                            @if ($pelamar->pernah_bekerja_di_al_azhar === 'Ya')
                                <div><dt class="text-muted-foreground">Lokasi Bekerja Sebelumnya</dt><dd class="font-medium">{{ $pelamar->lokasi_kerja_al_azhar_sebelumnya ?: '-' }}</dd></div>
                                <div><dt class="text-muted-foreground">Kapan Bekerja</dt><dd class="font-medium">{{ $pelamar->bulanKerjaAlAzharSebelumnyaLabel() ?? '-' }} {{ $pelamar->tahun_kerja_al_azhar_sebelumnya }}</dd></div>
                                <div><dt class="text-muted-foreground">Sebagai Apa</dt><dd class="font-medium">{{ $pelamar->jenisKepegawaianAlAzharLabel() ?? '-' }}</dd></div>
                            @endif
                        </dl>
                    </x-ui.card>

                    <x-ui.card title="Ubah Status">
                        <form method="POST" action="{{ route('admin.pelamar.status', $pelamar) }}" class="space-y-2">
                            @csrf
                            @method('PATCH')

                            @foreach ($statusOptions as $s)
                                @continue(in_array($s->id_status_pelamar, [\App\Models\StatusPelamar::SCREENING, \App\Models\StatusPelamar::ONGOING, \App\Models\StatusPelamar::LOLOS, \App\Models\StatusPelamar::DITOLAK], true))
                                @continue($s->id_status_pelamar === \App\Models\StatusPelamar::DICADANGKAN && $pelamar->id_tahap_rekrutmen < \App\Models\TahapRekrutmen::WAWANCARA)
                                @continue($s->id_status_pelamar === \App\Models\StatusPelamar::DITERIMA && $pelamar->id_tahap_rekrutmen !== \App\Models\TahapRekrutmen::TERIMA_SK)
                                @continue($s->id_status_pelamar === \App\Models\StatusPelamar::MIGRATED && $pelamar->id_tahap_rekrutmen !== \App\Models\TahapRekrutmen::MIGRASI_DATA)

                                @php
                                    $btnClass = match ($s->id_status_pelamar) {
                                        \App\Models\StatusPelamar::TIDAK_LOLOS => 'bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80',
                                        \App\Models\StatusPelamar::DICADANGKAN => 'bg-amber-500 text-white shadow-sm hover:bg-amber-400',
                                        \App\Models\StatusPelamar::MUNDUR => 'bg-slate-600 text-white shadow-sm hover:bg-slate-500',
                                        \App\Models\StatusPelamar::DITERIMA, \App\Models\StatusPelamar::MIGRATED => 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-500',
                                        default => 'bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80',
                                    };
                                @endphp
                                <x-ui.button type="submit" name="id_status_pelamar" value="{{ $s->id_status_pelamar }}" class="w-full {{ $btnClass }}">
                                    Tandai {{ ucfirst($s->status_pelamar) }}
                                </x-ui.button>
                            @endforeach
                        </form>

                        <div class="grid grid-cols-2 gap-2 mt-3">
                            @if ($pelamar->id_tahap_rekrutmen > \App\Models\TahapRekrutmen::SELEKSI_BERKAS)
                                <form method="POST" action="{{ route('admin.pelamar.mundur', $pelamar) }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-ui.button type="submit" variant="outline" class="w-full">
                                        &larr; Mundurkan
                                    </x-ui.button>
                                </form>
                            @endif

                            @if (! in_array($pelamar->id_status_pelamar, [\App\Models\StatusPelamar::MUNDUR, \App\Models\StatusPelamar::DICADANGKAN], true) && $pelamar->id_tahap_rekrutmen < \App\Models\TahapRekrutmen::MIGRASI_DATA)
                                <form method="POST" action="{{ route('admin.pelamar.lanjut', $pelamar) }}" class="{{ $pelamar->id_tahap_rekrutmen > \App\Models\TahapRekrutmen::SELEKSI_BERKAS ? '' : 'col-span-2' }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-ui.button type="submit" class="w-full">
                                        Lanjutkan &rarr;
                                    </x-ui.button>
                                </form>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.pelamar.catatan', $pelamar) }}" class="mt-4 pt-4 border-t space-y-2">
                            @csrf
                            @method('PATCH')
                            <x-ui.label for="catatan">Catatan</x-ui.label>
                            <x-ui.textarea id="catatan" name="catatan" rows="2" placeholder="Tulis catatan...">{{ $pelamar->catatan }}</x-ui.textarea>
                            <x-ui.button type="submit" variant="secondary" class="w-full">Simpan Catatan</x-ui.button>
                        </form>
                    </x-ui.card>
                </div>

                <x-ui.card title="Pendidikan & Berkas">
                    @php
                        $pendidikanLabel = $pelamar->pendidikanTerakhir->pendidikan_terakhir;
                        $isSekolahMenengah = in_array($pendidikanLabel, ['SMP', 'SMA'], true);
                        $akreditasiLabel = ['A' => 'Unggul', 'B' => 'Baik Sekali', 'C' => 'Baik'][$pelamar->akreditasi] ?? null;
                    @endphp
                    <dl class="grid sm:grid-cols-2 gap-4 text-sm pb-4 mb-4 border-b">
                        <div><dt class="text-muted-foreground">Pendidikan Terakhir</dt><dd class="font-medium">{{ $pendidikanLabel }}</dd></div>
                        <div><dt class="text-muted-foreground">{{ $isSekolahMenengah ? 'Nama Sekolah' : 'Institusi / Perguruan Tinggi' }}</dt><dd class="font-medium">{{ $pelamar->institusi }}</dd></div>
                        @unless ($isSekolahMenengah)
                            <div><dt class="text-muted-foreground">Program Studi</dt><dd class="font-medium">{{ $pelamar->program_studi ?: '-' }}</dd></div>
                            <div><dt class="text-muted-foreground">Akreditasi</dt><dd class="font-medium">{{ $pelamar->akreditasi ?: '-' }}{{ $akreditasiLabel ? ' ('.$akreditasiLabel.')' : '' }}</dd></div>
                        @endunless
                        <div><dt class="text-muted-foreground">Tahun Lulus</dt><dd class="font-medium">{{ $pelamar->tahun_lulus }}</dd></div>
                        @if ($pendidikanLabel === 'D3')
                            <div><dt class="text-muted-foreground">Kategori Perguruan Tinggi D3</dt><dd class="font-medium">{{ $pelamar->kategori_perguruan_tinggi_d3 ?: '-' }}</dd></div>
                            <div><dt class="text-muted-foreground">IPK D3</dt><dd class="font-medium">{{ $pelamar->ipk_d3 ? number_format((float) $pelamar->ipk_d3, 2) : '-' }}</dd></div>
                        @endif
                        @if (in_array($pendidikanLabel, ['S1', 'S2', 'S3']))
                            <div><dt class="text-muted-foreground">Kategori Perguruan Tinggi S1</dt><dd class="font-medium">{{ $pelamar->kategori_perguruan_tinggi_s1 ?: '-' }}</dd></div>
                            <div><dt class="text-muted-foreground">IPK S1</dt><dd class="font-medium">{{ $pelamar->ipk_s1 ? number_format((float) $pelamar->ipk_s1, 2) : '-' }}</dd></div>
                        @endif
                        @if (in_array($pendidikanLabel, ['S2', 'S3']))
                            <div><dt class="text-muted-foreground">Kategori Perguruan Tinggi S2</dt><dd class="font-medium">{{ $pelamar->kategori_perguruan_tinggi_s2 ?: '-' }}</dd></div>
                            <div><dt class="text-muted-foreground">IPK S2</dt><dd class="font-medium">{{ $pelamar->ipk_s2 ? number_format((float) $pelamar->ipk_s2, 2) : '-' }}</dd></div>
                        @endif
                        @if ($pendidikanLabel === 'S3')
                            <div><dt class="text-muted-foreground">Kategori Perguruan Tinggi S3</dt><dd class="font-medium">{{ $pelamar->kategori_perguruan_tinggi_s3 ?: '-' }}</dd></div>
                            <div><dt class="text-muted-foreground">IPK S3</dt><dd class="font-medium">{{ $pelamar->ipk_s3 ? number_format((float) $pelamar->ipk_s3, 2) : '-' }}</dd></div>
                        @endif
                    </dl>

                    @php
                            $berkasList = collect([
                                ['icon' => '📄', 'label' => 'CV', 'url' => $pelamar->cvUrl(), 'path' => $pelamar->cv_upload],
                                ['icon' => '📷', 'label' => 'Pas Foto', 'url' => $pelamar->pasFotoUrl(), 'path' => $pelamar->pas_foto_upload],
                                ['icon' => '📄', 'label' => 'Ijazah', 'url' => $pelamar->ijazahUrl(), 'path' => $pelamar->ijazah_upload],
                                ['icon' => '🪪', 'label' => 'KTP', 'url' => $pelamar->ktpUrl(), 'path' => $pelamar->ktp_upload],
                                ['icon' => '🪪', 'label' => 'SIM', 'url' => $pelamar->simUrl(), 'path' => $pelamar->sim_upload],
                                ['icon' => '📄', 'label' => 'Transkrip Nilai', 'url' => $pelamar->transkripUrl(), 'path' => $pelamar->transkrip_nilai_upload],
                                ['icon' => '📄', 'label' => 'Surat Lamaran', 'url' => $pelamar->suratLamaranUrl(), 'path' => $pelamar->surat_lamaran_upload],
                                ['icon' => '📄', 'label' => 'Sertifikat Gada Pratama', 'url' => $pelamar->sertifikatGadaPratamaUrl(), 'path' => $pelamar->sertifikat_gada_pratama_upload],
                                ['icon' => '📄', 'label' => 'Sertifikat Tambahan', 'url' => $pelamar->sertifikatTambahanUrl(), 'path' => $pelamar->sertifikat_tambahan_upload],
                            ])->filter(fn ($b) => $b['url']);
                        @endphp
    
                        <div
                            x-data="{
                                open: false,
                                url: null,
                                label: null,
                                ext: null,
                                viewer: null,
                                imageExts: ['jpg', 'jpeg', 'png'],
                                showBerkas(url, label, ext) {
                                    this.label = label;
                                    this.ext = ext;
                                    if (this.imageExts.includes(ext)) {
                                        this.$nextTick(() => {
                                            this.$refs.viewerImg.src = url;
                                            if (this.viewer) this.viewer.destroy();
                                            this.viewer = new Viewer(this.$refs.viewerImg, {
                                                inline: false,
                                                navbar: false,
                                                title: [1, (image) => label],
                                                toolbar: {
                                                    zoomIn: 1, zoomOut: 1, oneToOne: 1, reset: 1,
                                                    rotateLeft: 1, rotateRight: 1, flipHorizontal: 1, flipVertical: 1,
                                                },
                                                hidden: () => { this.viewer.destroy(); this.viewer = null; },
                                            });
                                            this.viewer.show();
                                        });
                                    } else {
                                        this.url = url;
                                        this.open = true;
                                    }
                                },
                            }"
                            class="flex flex-wrap gap-3 text-sm"
                        >
                            <img x-ref="viewerImg" class="hidden" alt="">
    
                            @foreach ($berkasList as $berkas)
                                <x-ui.button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    @click="showBerkas({{ json_encode($berkas['url']) }}, {{ json_encode($berkas['label']) }}, {{ json_encode(strtolower(pathinfo($berkas['path'], PATHINFO_EXTENSION))) }})"
                                >{{ $berkas['icon'] }} {{ $berkas['label'] }}</x-ui.button>
                            @endforeach
    
                            <div x-show="open" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4">
                                <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
                                <div
                                    x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="relative w-full max-w-3xl max-h-[90vh] flex flex-col rounded-lg border bg-background p-4 shadow-lg text-left"
                                >
                                    <div class="flex items-center justify-between gap-4 mb-3 shrink-0">
                                        <h3 class="text-sm font-semibold" x-text="label"></h3>
                                        <div class="flex items-center gap-3">
                                            <a :href="url" target="_blank" class="text-xs text-muted-foreground hover:text-foreground underline">Buka di tab baru</a>
                                            <button type="button" @click="open = false" class="shrink-0 text-muted-foreground hover:text-foreground">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
    
                                    <div class="flex-1 overflow-auto">
                                        <template x-if="ext === 'pdf'">
                                            <iframe :src="url" class="w-full h-[75vh] rounded border"></iframe>
                                        </template>
                                        <template x-if="ext && ext !== 'pdf'">
                                            <div class="text-sm text-muted-foreground text-center py-10">
                                                <p>Pratinjau tidak tersedia untuk format berkas ini (.<span x-text="ext"></span>).</p>
                                                <a :href="url" target="_blank" class="inline-flex mt-3 h-8 items-center rounded-md border border-input bg-background px-3 text-xs font-medium shadow-sm hover:bg-accent">Unduh Berkas</a>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-ui.card>

                @if ($pelamar->id_tahap_rekrutmen >= \App\Models\TahapRekrutmen::TES_TULIS)
                    <x-ui.card title="Tes Tulis">
                        <form method="POST" action="{{ route('admin.pelamar.tes-tulis', $pelamar) }}" class="grid sm:grid-cols-2 gap-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <x-ui.label for="nilai_tes_agama_umum">Nilai Tes Agama Umum</x-ui.label>
                                <x-ui.input type="number" id="nilai_tes_agama_umum" name="nilai_tes_agama_umum" step="0.01" min="0" max="100" value="{{ old('nilai_tes_agama_umum', $pelamar->tesTulis?->nilai_tes_agama_umum) }}" />
                            </div>
                            <div>
                                <x-ui.label for="nilai_tes_bidang_studi">Nilai Tes Bidang Studi</x-ui.label>
                                <x-ui.input type="number" id="nilai_tes_bidang_studi" name="nilai_tes_bidang_studi" step="0.01" min="0" max="100" value="{{ old('nilai_tes_bidang_studi', $pelamar->tesTulis?->nilai_tes_bidang_studi) }}" />
                            </div>
                            <div>
                                <x-ui.label for="nilai_tes_inggris_umum">Nilai Tes Inggris Umum</x-ui.label>
                                <x-ui.input type="number" id="nilai_tes_inggris_umum" name="nilai_tes_inggris_umum" step="0.01" min="0" max="100" value="{{ old('nilai_tes_inggris_umum', $pelamar->tesTulis?->nilai_tes_inggris_umum) }}" />
                            </div>
                            <div>
                                <x-ui.label for="tt_tanggal_pelaksanaan">Tanggal &amp; Jam Pelaksanaan</x-ui.label>
                                <x-ui.input type="datetime-local" id="tt_tanggal_pelaksanaan" name="tanggal_pelaksanaan" value="{{ old('tanggal_pelaksanaan', optional($pelamar->tesTulis?->tanggal_pelaksanaan)->format('Y-m-d\TH:i')) }}" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-ui.button type="submit" variant="secondary">Simpan Tes Tulis</x-ui.button>
                            </div>
                        </form>
                    </x-ui.card>
                @endif

                @if ($pelamar->id_tahap_rekrutmen >= \App\Models\TahapRekrutmen::WAWANCARA)
                    <x-ui.card title="Wawancara">
                        <div class="grid sm:grid-cols-2 gap-4 text-sm pb-4 mb-4 border-b">
                            <div><dt class="text-muted-foreground inline">Nilai Rata-rata Tes Tulis: </dt><dd class="font-medium inline">{{ $pelamar->tesTulis?->nilaiRataRata() ?? '-' }}</dd></div>
                            <div><dt class="text-muted-foreground inline">Nilai Rata-rata Wawancara: </dt><dd class="font-medium inline">{{ $pelamar->wawancara?->nilaiRataRataWawancara() ?? '-' }}</dd></div>
                        </div>
                        <form method="POST" action="{{ route('admin.pelamar.wawancara', $pelamar) }}" class="grid sm:grid-cols-2 gap-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <x-ui.label for="nilai_wawancara_agama">Nilai Wawancara Agama</x-ui.label>
                                <x-ui.input type="number" id="nilai_wawancara_agama" name="nilai_wawancara_agama" step="0.01" min="0" max="100" value="{{ old('nilai_wawancara_agama', $pelamar->wawancara?->nilai_wawancara_agama) }}" />
                            </div>
                            <div>
                                <x-ui.label for="nilai_praktik_micro_teaching">Nilai Praktik/Micro Teaching</x-ui.label>
                                <x-ui.input type="number" id="nilai_praktik_micro_teaching" name="nilai_praktik_micro_teaching" step="0.01" min="0" max="100" value="{{ old('nilai_praktik_micro_teaching', $pelamar->wawancara?->nilai_praktik_micro_teaching) }}" />
                            </div>
                            <div>
                                <x-ui.label for="nilai_wawancara_umum">Nilai Wawancara Umum</x-ui.label>
                                <x-ui.input type="number" id="nilai_wawancara_umum" name="nilai_wawancara_umum" step="0.01" min="0" max="100" value="{{ old('nilai_wawancara_umum', $pelamar->wawancara?->nilai_wawancara_umum) }}" />
                            </div>
                            <div>
                                <x-ui.label for="w_tanggal_pelaksanaan">Tanggal &amp; Jam Pelaksanaan</x-ui.label>
                                <x-ui.input type="datetime-local" id="w_tanggal_pelaksanaan" name="tanggal_pelaksanaan" value="{{ old('tanggal_pelaksanaan', optional($pelamar->wawancara?->tanggal_pelaksanaan)->format('Y-m-d\TH:i')) }}" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-ui.button type="submit" variant="secondary">Simpan Wawancara</x-ui.button>
                            </div>
                        </form>
                    </x-ui.card>
                @endif

                @if ($pelamar->id_tahap_rekrutmen >= \App\Models\TahapRekrutmen::ORIENTASI)
                    <x-ui.card title="Orientasi">
                        <form method="POST" action="{{ route('admin.pelamar.orientasi', $pelamar) }}" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <x-ui.label for="id_unit_kerja">Penempatan (Unit Kerja)</x-ui.label>
                                <x-ui.select id="id_unit_kerja" name="id_unit_kerja">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($unitKerjaList as $u)
                                        <option value="{{ $u->id_unit_kerja }}" @selected((string) old('id_unit_kerja', $pelamar->orientasi?->id_unit_kerja) === (string) $u->id_unit_kerja)>{{ $u->nama_unit }}</option>
                                    @endforeach
                                </x-ui.select>
                            </div>
                            <div></div>
                            <div>
                                <x-ui.label for="uang_makan">Uang Makan</x-ui.label>
                                <x-ui.input type="number" id="uang_makan" name="uang_makan" min="0" value="{{ old('uang_makan', $pelamar->orientasi?->uang_makan) }}" />
                            </div>
                            <div>
                                <x-ui.label for="uang_transport">Uang Transport</x-ui.label>
                                <x-ui.input type="number" id="uang_transport" name="uang_transport" min="0" value="{{ old('uang_transport', $pelamar->orientasi?->uang_transport) }}" />
                            </div>
                            <div>
                                <x-ui.label for="tanggal_mulai">Tanggal Mulai</x-ui.label>
                                <x-ui.input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', optional($pelamar->orientasi?->tanggal_mulai)->format('Y-m-d')) }}" />
                            </div>
                            <div>
                                <x-ui.label for="tanggal_selesai">Tanggal Selesai</x-ui.label>
                                <x-ui.input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', optional($pelamar->orientasi?->tanggal_selesai)->format('Y-m-d')) }}" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-ui.label for="sk_orientasi_upload">SK Orientasi (PDF)</x-ui.label>
                                <input type="file" id="sk_orientasi_upload" name="sk_orientasi_upload" accept=".pdf" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                                @if ($pelamar->orientasi?->skOrientasiUrl())
                                    <a href="{{ $pelamar->orientasi->skOrientasiUrl() }}" target="_blank" class="text-xs text-muted-foreground hover:text-foreground underline mt-1 inline-block">Lihat berkas saat ini</a>
                                @endif
                            </div>
                            <div class="sm:col-span-2">
                                <x-ui.button type="submit" variant="secondary">Simpan Orientasi</x-ui.button>
                            </div>
                        </form>
                    </x-ui.card>
                @endif

                @if ($pelamar->id_tahap_rekrutmen >= \App\Models\TahapRekrutmen::TUGAS_SEMENTARA)
                    <x-ui.card title="Tugas Sementara">
                        <form method="POST" action="{{ route('admin.pelamar.tugas-sementara', $pelamar) }}" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <x-ui.label for="sk_tugas_sementara_upload">SK Tugas Sementara (PDF)</x-ui.label>
                                <input type="file" id="sk_tugas_sementara_upload" name="sk_tugas_sementara_upload" accept=".pdf" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                                @if ($pelamar->tugasSementara?->skTugasSementaraUrl())
                                    <a href="{{ $pelamar->tugasSementara->skTugasSementaraUrl() }}" target="_blank" class="text-xs text-muted-foreground hover:text-foreground underline mt-1 inline-block">Lihat berkas saat ini</a>
                                @endif
                            </div>
                            <div>
                                <x-ui.label for="hasil_tes_kesehatan_upload">Hasil Tes Kesehatan (PDF)</x-ui.label>
                                <input type="file" id="hasil_tes_kesehatan_upload" name="hasil_tes_kesehatan_upload" accept=".pdf" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                                @if ($pelamar->tugasSementara?->hasilTesKesehatanUrl())
                                    <a href="{{ $pelamar->tugasSementara->hasilTesKesehatanUrl() }}" target="_blank" class="text-xs text-muted-foreground hover:text-foreground underline mt-1 inline-block">Lihat berkas saat ini</a>
                                @endif
                            </div>
                            <div class="sm:col-span-2">
                                <x-ui.button type="submit" variant="secondary">Simpan Tugas Sementara</x-ui.button>
                            </div>
                        </form>
                    </x-ui.card>
                @endif

                <x-ui.card title="Riwayat Tahap">
                    <ol class="space-y-3 max-h-96 overflow-y-auto pr-1">
                        @forelse ($pelamar->riwayat as $r)
                            <li class="text-sm border-l-2 border-border pl-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-medium">{{ $r->tahapRekrutmen->tahap_rekrutmen }}</span>
                                    <x-status-badge :status="$r->statusPelamar" />
                                    <span class="text-muted-foreground text-xs">{{ $r->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    @if ($r->created_by)
                                        <span class="text-muted-foreground text-xs">oleh {{ $r->created_by }}</span>
                                    @endif
                                </div>
                                @if ($r->catatan)
                                    <p class="text-muted-foreground mt-0.5">{{ $r->catatan }}</p>
                                @endif
                            </li>
                        @empty
                            <li class="text-sm text-muted-foreground">Belum ada riwayat.</li>
                        @endforelse
                    </ol>
                </x-ui.card>

                @if ($pelamar->logNotifikasi->isNotEmpty())
                    <x-ui.card title="Riwayat Notifikasi">
                        <ol class="space-y-2">
                            @foreach ($pelamar->logNotifikasi as $log)
                                <li class="text-sm border-l-2 border-border pl-3">
                                    <span class="font-medium">{{ $log->channel === 'whatsapp' ? 'WhatsApp' : 'Email' }}</span>
                                    <span class="text-muted-foreground text-xs">{{ $log->created_at->translatedFormat('d M Y, H:i') }} oleh {{ $log->created_by }}</span>
                                    <p class="text-muted-foreground mt-0.5 line-clamp-2">{{ $log->pesan ?: '(pesan kosong)' }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </x-ui.card>
                @endif
            </div>

            <x-ui.card title="Kirim Notifikasi">
                <x-notify-panel :pelamar="$pelamar" />
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
