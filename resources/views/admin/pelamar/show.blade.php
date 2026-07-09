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
                            <div><dt class="text-muted-foreground">Tanggal Apply</dt><dd class="font-medium">{{ $pelamar->tanggal_apply->translatedFormat('d M Y') }}</dd></div>
                            <div><dt class="text-muted-foreground">Tanggal Lahir</dt><dd class="font-medium">{{ $pelamar->tanggal_lahir->translatedFormat('d M Y') }}</dd></div>
                            <div><dt class="text-muted-foreground">Jenis Kelamin</dt><dd class="font-medium">{{ $pelamar->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
                            <div><dt class="text-muted-foreground">Email</dt><dd class="font-medium">{{ $pelamar->email ?: '-' }}</dd></div>
                            <div><dt class="text-muted-foreground">No. WhatsApp</dt><dd class="font-medium">{{ $pelamar->no_hp ?: '-' }}</dd></div>

                            <div><dt class="text-muted-foreground">Alamat</dt><dd class="font-medium">{{ $pelamar->alamat }}</dd></div>
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
                    <dl class="grid sm:grid-cols-2 gap-4 text-sm pb-4 mb-4 border-b">
                        <div><dt class="text-muted-foreground">Pendidikan Terakhir</dt><dd class="font-medium">{{ $pelamar->pendidikanTerakhir->pendidikan_terakhir }}</dd></div>
                        <div><dt class="text-muted-foreground">Institusi</dt><dd class="font-medium">{{ $pelamar->institusi }}</dd></div>
                        <div><dt class="text-muted-foreground">Program Studi</dt><dd class="font-medium">{{ $pelamar->program_studi }}</dd></div>
                        <div><dt class="text-muted-foreground">Akreditasi</dt><dd class="font-medium">{{ $pelamar->akreditasi }}</dd></div>
                        <div><dt class="text-muted-foreground">Tahun Lulus</dt><dd class="font-medium">{{ $pelamar->tahun_lulus }}</dd></div>
                        <div><dt class="text-muted-foreground">IPK</dt><dd class="font-medium">{{ number_format((float) $pelamar->ipk, 2) }}</dd></div>
                    </dl>

                    @php
                            $berkasList = collect([
                                ['icon' => '📄', 'label' => 'CV', 'url' => $pelamar->cvUrl(), 'path' => $pelamar->cv_upload],
                                ['icon' => '📄', 'label' => 'Ijazah', 'url' => $pelamar->ijazahUrl(), 'path' => $pelamar->ijazah_upload],
                                ['icon' => '🪪', 'label' => 'KTP', 'url' => $pelamar->ktpUrl(), 'path' => $pelamar->ktp_upload],
                                ['icon' => '📄', 'label' => 'Transkrip Nilai', 'url' => $pelamar->transkripUrl(), 'path' => $pelamar->transkrip_nilai_upload],
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
