<x-layouts.public :title="$loker->judul_loker.' - Rekrutmen YPI Al Azhar'">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('loker.list') }}" class="text-sm text-muted-foreground hover:text-foreground">&larr; Kembali ke daftar lowongan</a>

    @php
        $daysLeft = $loker->end_time ? (int) now()->startOfDay()->diffInDays($loker->end_time->copy()->startOfDay(), false) : null;
    @endphp

    <x-ui.card class="mt-4">
        <div class="flex items-start justify-between gap-2">
            <h1 class="text-xl font-semibold tracking-tight">{{ $loker->judul_loker }}</h1>
            @if ($daysLeft === null)
                <x-ui.badge variant="success" class="shrink-0">Dibuka</x-ui.badge>
            @elseif ($daysLeft <= 0)
                <x-ui.badge variant="destructive" class="shrink-0">Hari Terakhir</x-ui.badge>
            @else
                <x-ui.badge :variant="$daysLeft <= 7 ? 'destructive' : 'success'" class="shrink-0">H-{{ $daysLeft }}</x-ui.badge>
            @endif
        </div>

        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-muted-foreground">
            @if ($loker->lokasi)
                <span class="inline-flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-4 w-4 shrink-0 text-brand-navy-600"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                    {{ $loker->lokasi }}
                </span>
            @endif
            @if ($loker->end_time)
                <span class="inline-flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-4 w-4 shrink-0 text-brand-navy-600"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                    Berlaku sampai {{ $loker->end_time->translatedFormat('d M Y') }}
                </span>
            @endif
        </div>

        @if ($loker->deskripsi_loker)
            <p class="mt-4 text-sm text-foreground/90 whitespace-pre-line">{{ $loker->deskripsi_loker }}</p>
        @endif

        @php
            $bobotLabel = ['wajib' => 'Wajib', 'diutamakan' => 'Diutamakan', 'nilai_tambah' => 'Nilai Tambah'];
            $bobotVariant = ['wajib' => 'destructive', 'diutamakan' => 'warning', 'nilai_tambah' => 'secondary'];
        @endphp

        @if ($kriteriaByBobot->isNotEmpty())
            <div class="mt-6 space-y-4">
                @foreach (['wajib', 'diutamakan', 'nilai_tambah'] as $bobot)
                    @if ($kriteriaByBobot->has($bobot))
                        <div>
                            <h3 class="text-sm font-medium mb-2">{{ $bobotLabel[$bobot] }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($kriteriaByBobot[$bobot] as $k)
                                    <x-ui.badge :variant="$bobotVariant[$bobot]" class="font-normal">
                                        {{ $k->teksKriteria() ?? '(tanpa kriteria)' }}
                                    </x-ui.badge>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </x-ui.card>

    @php
        $step2Fields = ['id_pendidikan_terakhir', 'gelar', 'institusi', 'program_studi', 'kategori_perguruan_tinggi', 'akreditasi', 'tahun_lulus', 'ipk_s1', 'ipk_s2', 'ipk_s3', 'ipk_d3'];
        $step3Fields = ['cv_upload', 'ktp_upload', 'ijazah_upload', 'transkrip_nilai_upload', 'pas_foto_upload', 'surat_lamaran_upload', 'sim_upload', 'sertifikat_gada_pratama_upload', 'sertifikat_tambahan_upload', 'loker'];
        $errorStep = null;
        if ($errors->any()) {
            $errorFields = array_keys($errors->toArray());
            $errorStep = count(array_intersect($errorFields, $step3Fields)) ? 3
                : (count(array_intersect($errorFields, $step2Fields)) ? 2 : 1);
        }
    @endphp

    <x-ui.card title="Formulir Lamaran" class="mt-6">
      <div x-data="applyWizard({{ $loker->id_loker }}, @js(old()), @js($errorStep), @js($pendidikanList->pluck('pendidikan_terakhir', 'id_pendidikan_terakhir')))" x-init="init()">

        <!-- Stepper -->
        <div class="flex items-center mb-8">
            @foreach (['Data Pelamar', 'Pendidikan', 'Unggah Dokumen'] as $i => $label)
                @php($n = $i + 1)
                <div class="flex items-center {{ $i < 2 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center gap-1.5 {{ $i < 2 ? '' : 'shrink-0' }}">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full border text-sm font-semibold transition-colors"
                            :class="step > {{ $n }} ? 'bg-primary text-primary-foreground border-primary' : (step === {{ $n }} ? 'border-primary text-primary' : 'border-border text-muted-foreground')"
                        >
                            <template x-if="step > {{ $n }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            </template>
                            <template x-if="step <= {{ $n }}">
                                <span>{{ $n }}</span>
                            </template>
                        </div>
                        <span class="text-xs font-medium hidden sm:block" :class="step === {{ $n }} ? 'text-foreground' : 'text-muted-foreground'">{{ $label }}</span>
                    </div>
                    @if ($i < 2)
                        <div class="flex-1 h-px mx-2 -mt-5 sm:mt-0" :class="step > {{ $n }} ? 'bg-primary' : 'bg-border'"></div>
                    @endif
                </div>
            @endforeach
        </div>

        <form x-ref="form" method="POST" action="{{ route('loker.lamar', $loker) }}" enctype="multipart/form-data">
            @csrf

            <!-- Step 1: Data Pelamar -->
            <div x-ref="step1" x-show="step === 1" class="space-y-5">
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <x-ui.label for="nama">Nama Sesuai KTP <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.input type="text" id="nama" name="nama" x-model="fields.nama" value="{{ old('nama') }}" required />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="nik">NIK <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.input type="text" id="nik" name="nik" x-model="fields.nik" value="{{ old('nik') }}" required maxlength="16" inputmode="numeric" placeholder="16 digit sesuai KTP" @input="nikIsDuplicate = false" @input.debounce.600ms="checkNik()" @blur="checkNik()" />
                        <p class="text-xs text-muted-foreground mt-1" x-show="nikChecking" x-cloak>Memeriksa NIK&hellip;</p>
                        <p class="text-xs text-destructive mt-1" x-show="nikIsDuplicate && !nikChecking" x-cloak>NIK ini sudah pernah digunakan untuk melamar lowongan ini.</p>
                        <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="tanggal_lahir">Tanggal Lahir <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.input type="date" id="tanggal_lahir" name="tanggal_lahir" x-model="fields.tanggal_lahir" value="{{ old('tanggal_lahir') }}" required max="{{ now()->subYears(18)->format('Y-m-d') }}" @change="checkAge()" />
                        <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="jenis_kelamin">Jenis Kelamin <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.select id="jenis_kelamin" name="jenis_kelamin" x-model="fields.jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="email">Email <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.input type="email" id="email" name="email" x-model="fields.email" value="{{ old('email') }}" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="no_hp">No. WhatsApp <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.input type="text" id="no_hp" name="no_hp" x-model="fields.no_hp" value="{{ old('no_hp') }}" required placeholder="08xxxxxxxxxx" />
                        <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.label for="alamat">Alamat <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.textarea id="alamat" name="alamat" x-model="fields.alamat" rows="3" required>{{ old('alamat') }}</x-ui.textarea>
                        <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.label for="pernah_rekrutmen_sebelumnya">Apakah pernah mengikuti rekrutmen di YPI Al Azhar? <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.select id="pernah_rekrutmen_sebelumnya" name="pernah_rekrutmen_sebelumnya" x-model="fields.pernah_rekrutmen_sebelumnya" required>
                            <option value="">-- Pilih --</option>
                            <option value="Ya" @selected(old('pernah_rekrutmen_sebelumnya') === 'Ya')>Ya</option>
                            <option value="Tidak" @selected(old('pernah_rekrutmen_sebelumnya') === 'Tidak')>Tidak</option>
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('pernah_rekrutmen_sebelumnya')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2 grid sm:grid-cols-3 gap-5" x-show="fields.pernah_rekrutmen_sebelumnya === 'Ya'" x-cloak>
                        <div class="sm:col-span-3">
                            <x-ui.label>Jika pernah, kapan dan sampai tahap apa?</x-ui.label>
                        </div>
                        <div>
                            <x-ui.select id="bulan_rekrutmen_sebelumnya" name="bulan_rekrutmen_sebelumnya" x-model="fields.bulan_rekrutmen_sebelumnya" x-bind:required="fields.pernah_rekrutmen_sebelumnya === 'Ya'">
                                <option value="">-- Bulan --</option>
                                @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $i => $bulanNama)
                                    <option value="{{ $i + 1 }}" @selected((string) old('bulan_rekrutmen_sebelumnya') === (string) ($i + 1))>{{ $bulanNama }}</option>
                                @endforeach
                            </x-ui.select>
                            <x-input-error :messages="$errors->get('bulan_rekrutmen_sebelumnya')" class="mt-2" />
                        </div>
                        <div>
                            <x-ui.select id="tahun_rekrutmen_sebelumnya" name="tahun_rekrutmen_sebelumnya" x-model="fields.tahun_rekrutmen_sebelumnya" x-bind:required="fields.pernah_rekrutmen_sebelumnya === 'Ya'">
                                <option value="">-- Tahun --</option>
                                @for ($tahun = 2020; $tahun <= 2030; $tahun++)
                                    <option value="{{ $tahun }}" @selected((string) old('tahun_rekrutmen_sebelumnya') === (string) $tahun)>{{ $tahun }}</option>
                                @endfor
                            </x-ui.select>
                            <x-input-error :messages="$errors->get('tahun_rekrutmen_sebelumnya')" class="mt-2" />
                        </div>
                        <div>
                            <x-ui.select id="id_tahap_rekrutmen_sebelumnya" name="id_tahap_rekrutmen_sebelumnya" x-model="fields.id_tahap_rekrutmen_sebelumnya" x-bind:required="fields.pernah_rekrutmen_sebelumnya === 'Ya'">
                                <option value="">-- Sampai Tahap --</option>
                                @foreach ($tahapList as $t)
                                    <option value="{{ $t->id_tahap_rekrutmen }}" @selected((string) old('id_tahap_rekrutmen_sebelumnya') === (string) $t->id_tahap_rekrutmen)>{{ $t->tahap_rekrutmen }}</option>
                                @endforeach
                            </x-ui.select>
                            <x-input-error :messages="$errors->get('id_tahap_rekrutmen_sebelumnya')" class="mt-2" />
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.label for="pernah_bekerja_di_al_azhar">Apakah pernah bekerja di Al Azhar? <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.select id="pernah_bekerja_di_al_azhar" name="pernah_bekerja_di_al_azhar" x-model="fields.pernah_bekerja_di_al_azhar" required>
                            <option value="">-- Pilih --</option>
                            <option value="Ya" @selected(old('pernah_bekerja_di_al_azhar') === 'Ya')>Ya</option>
                            <option value="Tidak" @selected(old('pernah_bekerja_di_al_azhar') === 'Tidak')>Tidak</option>
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('pernah_bekerja_di_al_azhar')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2 grid sm:grid-cols-2 gap-4" x-show="fields.pernah_bekerja_di_al_azhar === 'Ya'" x-cloak>
                        <div :class="fields.jenis_kepegawaian_al_azhar_sebelumnya === 'Lain-lain' ? '' : 'sm:col-span-2'">
                            <x-ui.label for="jenis_kepegawaian_al_azhar_sebelumnya">Sebagai apa? <span class="text-destructive">*</span></x-ui.label>
                            <x-ui.select id="jenis_kepegawaian_al_azhar_sebelumnya" name="jenis_kepegawaian_al_azhar_sebelumnya" x-model="fields.jenis_kepegawaian_al_azhar_sebelumnya" x-bind:required="fields.pernah_bekerja_di_al_azhar === 'Ya'">
                                <option value="">-- Pilih --</option>
                                <option value="Pegawai Honor" @selected(old('jenis_kepegawaian_al_azhar_sebelumnya') === 'Pegawai Honor')>Pegawai Honor</option>
                                <option value="Pegawai Tetap" @selected(old('jenis_kepegawaian_al_azhar_sebelumnya') === 'Pegawai Tetap')>Pegawai Tetap</option>
                                <option value="Pegawai Inval" @selected(old('jenis_kepegawaian_al_azhar_sebelumnya') === 'Pegawai Inval')>Pegawai Inval</option>
                                <option value="Pegawai Ekskul" @selected(old('jenis_kepegawaian_al_azhar_sebelumnya') === 'Pegawai Ekskul')>Pegawai Ekskul</option>
                                <option value="Lain-lain" @selected(old('jenis_kepegawaian_al_azhar_sebelumnya') === 'Lain-lain')>Lain-lain</option>
                            </x-ui.select>
                            <x-input-error :messages="$errors->get('jenis_kepegawaian_al_azhar_sebelumnya')" class="mt-2" />
                        </div>
                        <div x-show="fields.jenis_kepegawaian_al_azhar_sebelumnya === 'Lain-lain'" x-cloak>
                            <x-ui.label for="jenis_kepegawaian_al_azhar_lainnya">Sebutkan <span class="text-destructive">*</span></x-ui.label>
                            <x-ui.input type="text" id="jenis_kepegawaian_al_azhar_lainnya" name="jenis_kepegawaian_al_azhar_lainnya" x-model="fields.jenis_kepegawaian_al_azhar_lainnya" value="{{ old('jenis_kepegawaian_al_azhar_lainnya') }}" x-bind:required="fields.pernah_bekerja_di_al_azhar === 'Ya' && fields.jenis_kepegawaian_al_azhar_sebelumnya === 'Lain-lain'" />
                            <x-input-error :messages="$errors->get('jenis_kepegawaian_al_azhar_lainnya')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-ui.label for="lokasi_kerja_al_azhar_sebelumnya">Dimana? <span class="text-destructive">*</span></x-ui.label>
                            <x-ui.input type="text" id="lokasi_kerja_al_azhar_sebelumnya" name="lokasi_kerja_al_azhar_sebelumnya" x-model="fields.lokasi_kerja_al_azhar_sebelumnya" value="{{ old('lokasi_kerja_al_azhar_sebelumnya') }}" x-bind:required="fields.pernah_bekerja_di_al_azhar === 'Ya'" />
                            <x-input-error :messages="$errors->get('lokasi_kerja_al_azhar_sebelumnya')" class="mt-2" />
                        </div>
                        <div
                            class="sm:col-span-2 relative"
                            x-data="{
                                open: false,
                                bulanNama: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                                pickerYear: fields.kerja_al_azhar_periode ? parseInt(fields.kerja_al_azhar_periode.split('-')[0]) : new Date().getFullYear(),
                                get display() {
                                    if (!fields.kerja_al_azhar_periode) return '';
                                    const [y, m] = fields.kerja_al_azhar_periode.split('-');
                                    return this.bulanNama[parseInt(m) - 1] + ' ' + y;
                                },
                                pick(monthIndex) {
                                    fields.kerja_al_azhar_periode = this.pickerYear + '-' + String(monthIndex + 1).padStart(2, '0');
                                    this.open = false;
                                },
                            }"
                        >
                            <x-ui.label for="kerja_al_azhar_periode_btn">Kapan <span class="text-destructive">*</span></x-ui.label>
                            <button
                                type="button"
                                id="kerja_al_azhar_periode_btn"
                                @click="open = !open"
                                @click.outside="open = false"
                                class="mt-1.5 flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm"
                            >
                                <span :class="display ? '' : 'text-muted-foreground'" x-text="display || 'Pilih bulan & tahun'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-4 w-4 shrink-0 text-muted-foreground"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                            </button>
                            <input type="hidden" name="kerja_al_azhar_periode" :value="fields.kerja_al_azhar_periode" x-bind:required="fields.pernah_bekerja_di_al_azhar === 'Ya'" />

                            <div
                                x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute z-30 mt-1.5 w-64 rounded-lg border bg-popover text-popover-foreground p-3 shadow-lg"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <button type="button" @click="pickerYear--" class="flex h-7 w-7 items-center justify-center rounded-md hover:bg-accent">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                                    </button>
                                    <span class="text-sm font-semibold" x-text="pickerYear"></span>
                                    <button type="button" @click="pickerYear++" class="flex h-7 w-7 items-center justify-center rounded-md hover:bg-accent">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-3 gap-1">
                                    <template x-for="(bulan, i) in bulanNama" :key="i">
                                        <button
                                            type="button"
                                            @click="pick(i)"
                                            class="rounded-md px-2 py-1.5 text-xs hover:bg-accent"
                                            :class="fields.kerja_al_azhar_periode === (pickerYear + '-' + String(i + 1).padStart(2, '0')) ? 'bg-primary text-primary-foreground hover:bg-primary' : ''"
                                            x-text="bulan.slice(0, 3)"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('kerja_al_azhar_periode')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Pendidikan -->
            <div x-ref="step2" x-show="step === 2" x-cloak class="space-y-5">
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <x-ui.label for="id_pendidikan_terakhir">Pendidikan Terakhir <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.select id="id_pendidikan_terakhir" name="id_pendidikan_terakhir" x-model="fields.id_pendidikan_terakhir" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($pendidikanList as $p)
                                <option value="{{ $p->id_pendidikan_terakhir }}" @selected(old('id_pendidikan_terakhir') == $p->id_pendidikan_terakhir)>{{ $p->pendidikan_terakhir }}</option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('id_pendidikan_terakhir')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="gelar">Gelar (opsional)</x-ui.label>
                        <x-ui.input type="text" id="gelar" name="gelar" x-model="fields.gelar" value="{{ old('gelar') }}" placeholder="cth. S.Pd." />
                        <x-input-error :messages="$errors->get('gelar')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.label for="institusi">
                            <span x-text="isSekolahMenengah ? 'Nama Sekolah' : 'Institusi / Perguruan Tinggi'">Institusi / Sekolah</span> <span class="text-destructive">*</span>
                        </x-ui.label>
                        <x-ui.input type="text" id="institusi" name="institusi" x-model="fields.institusi" value="{{ old('institusi') }}" required />
                        <x-input-error :messages="$errors->get('institusi')" class="mt-2" />
                    </div>
                    <div x-show="!isSekolahMenengah" x-cloak>
                        <x-ui.label for="program_studi">Program Studi / Jurusan <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.input type="text" id="program_studi" name="program_studi" x-model="fields.program_studi" value="{{ old('program_studi') }}" x-bind:required="!isSekolahMenengah" />
                        <x-input-error :messages="$errors->get('program_studi')" class="mt-2" />
                    </div>
                    <div x-show="!isSekolahMenengah" x-cloak>
                        <x-ui.label for="kategori_perguruan_tinggi">Kategori Perguruan Tinggi <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.select id="kategori_perguruan_tinggi" name="kategori_perguruan_tinggi" x-model="fields.kategori_perguruan_tinggi" x-bind:required="!isSekolahMenengah">
                            <option value="">-- Pilih --</option>
                            @foreach (['Perguruan Tinggi Negeri', 'Perguruan Tinggi Swasta', 'Lain-lain'] as $kat)
                                <option value="{{ $kat }}" @selected(old('kategori_perguruan_tinggi') === $kat)>{{ $kat }}</option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('kategori_perguruan_tinggi')" class="mt-2" />
                    </div>
                    <div x-show="!isSekolahMenengah" x-cloak>
                        <x-ui.label for="akreditasi">Akreditasi Program Studi Saat Lulus <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.select id="akreditasi" name="akreditasi" x-model="fields.akreditasi" x-bind:required="!isSekolahMenengah">
                            <option value="">-- Pilih --</option>
                            <option value="A" @selected(old('akreditasi') === 'A')>A &mdash; Unggul</option>
                            <option value="B" @selected(old('akreditasi') === 'B')>B &mdash; Baik Sekali</option>
                            <option value="C" @selected(old('akreditasi') === 'C')>C &mdash; Baik</option>
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('akreditasi')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="tahun_lulus">Tahun Lulus <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.select id="tahun_lulus" name="tahun_lulus" x-model="fields.tahun_lulus" required>
                            <option value="">-- Pilih --</option>
                            @for ($tahun = 2012; $tahun <= 2026; $tahun++)
                                <option value="{{ $tahun }}" @selected((string) old('tahun_lulus') === (string) $tahun)>{{ $tahun }}</option>
                            @endfor
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('tahun_lulus')" class="mt-2" />
                    </div>
                    <div x-show="isD3" x-cloak>
                        <x-ui.label for="ipk_d3">IPK D3 <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.input type="number" id="ipk_d3" name="ipk_d3" x-model="fields.ipk_d3" value="{{ old('ipk_d3') }}" min="0" max="4" step="0.01" placeholder="cth. 3.50" x-bind:required="isD3" />
                        <x-input-error :messages="$errors->get('ipk_d3')" class="mt-2" />
                    </div>
                    <div x-show="isS1 || isS2 || isS3" x-cloak>
                        <x-ui.label for="ipk_s1">IPK S1 <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.input type="number" id="ipk_s1" name="ipk_s1" x-model="fields.ipk_s1" value="{{ old('ipk_s1') }}" min="0" max="4" step="0.01" placeholder="cth. 3.50" x-bind:required="isS1 || isS2 || isS3" />
                        <x-input-error :messages="$errors->get('ipk_s1')" class="mt-2" />
                    </div>
                    <div x-show="isS2 || isS3" x-cloak>
                        <x-ui.label for="ipk_s2">IPK S2 <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.input type="number" id="ipk_s2" name="ipk_s2" x-model="fields.ipk_s2" value="{{ old('ipk_s2') }}" min="0" max="4" step="0.01" placeholder="cth. 3.50" x-bind:required="isS2 || isS3" />
                        <x-input-error :messages="$errors->get('ipk_s2')" class="mt-2" />
                    </div>
                    <div x-show="isS3" x-cloak>
                        <x-ui.label for="ipk_s3">IPK S3 <span class="text-destructive">*</span></x-ui.label>
                        <x-ui.input type="number" id="ipk_s3" name="ipk_s3" x-model="fields.ipk_s3" value="{{ old('ipk_s3') }}" min="0" max="4" step="0.01" placeholder="cth. 3.50" x-bind:required="isS3" />
                        <x-input-error :messages="$errors->get('ipk_s3')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Step 3: Unggah Dokumen -->
            <div x-ref="step3" x-show="step === 3" x-cloak class="space-y-5">
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <x-ui.label for="cv_upload">Curriculum Vitae <span class="text-destructive">*</span></x-ui.label>
                        <input type="file" id="cv_upload" name="cv_upload" required accept=".pdf" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                        <p class="text-xs text-muted-foreground mt-1">Format PDF, maksimal 5MB.</p>
                        <x-input-error :messages="$errors->get('cv_upload')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="pas_foto_upload">Pas Foto 3x4 <span class="text-destructive">*</span></x-ui.label>
                        <input type="file" id="pas_foto_upload" name="pas_foto_upload" required accept=".jpg,.jpeg,.png" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                        <p class="text-xs text-muted-foreground mt-1">Format JPG/PNG, maksimal 5MB.</p>
                        <x-input-error :messages="$errors->get('pas_foto_upload')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="ktp_upload">KTP <span class="text-destructive">*</span></x-ui.label>
                        <input type="file" id="ktp_upload" name="ktp_upload" required accept=".jpg,.jpeg,.png" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                        <p class="text-xs text-muted-foreground mt-1">Format JPG/PNG, maksimal 5MB.</p>
                        <x-input-error :messages="$errors->get('ktp_upload')" class="mt-2" />
                    </div>
                    @if ($loker->jenjang?->nama_jenjang === 'Driver')
                        <div>
                            <x-ui.label for="sim_upload">SIM <span class="text-destructive">*</span></x-ui.label>
                            <input type="file" id="sim_upload" name="sim_upload" required accept=".jpg,.jpeg,.png" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                            <p class="text-xs text-muted-foreground mt-1">Format JPG/PNG, maksimal 5MB.</p>
                            <x-input-error :messages="$errors->get('sim_upload')" class="mt-2" />
                        </div>
                    @endif
                    <div>
                        <x-ui.label for="ijazah_upload">Ijazah <span class="text-destructive">*</span></x-ui.label>
                        <input type="file" id="ijazah_upload" name="ijazah_upload" required accept=".pdf" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                        <p class="text-xs text-muted-foreground mt-1">Format PDF, maksimal 5MB. Sertakan ijazah dari jenjang pendidikan awal hingga pendidikan terakhir (dapat digabungkan dalam satu file PDF).</p>
                        <x-input-error :messages="$errors->get('ijazah_upload')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="transkrip_nilai_upload">Transkrip Nilai <span class="text-destructive">*</span></x-ui.label>
                        <input type="file" id="transkrip_nilai_upload" name="transkrip_nilai_upload" required accept=".pdf" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                        <p class="text-xs text-muted-foreground mt-1">Format PDF, maksimal 5MB.</p>
                        <x-input-error :messages="$errors->get('transkrip_nilai_upload')" class="mt-2" />
                    </div>
                    <div>
                        <x-ui.label for="surat_lamaran_upload">Surat Lamaran <span class="text-destructive">*</span></x-ui.label>
                        <input type="file" id="surat_lamaran_upload" name="surat_lamaran_upload" required accept=".pdf" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                        <p class="text-xs text-muted-foreground mt-1">Format PDF, maksimal 5MB.</p>
                        <x-input-error :messages="$errors->get('surat_lamaran_upload')" class="mt-2" />
                    </div>
                    @if ($loker->jenjang?->nama_jenjang === 'Satpam')
                        <div>
                            <x-ui.label for="sertifikat_gada_pratama_upload">Sertifikat Gada Pratama <span class="text-destructive">*</span></x-ui.label>
                            <input type="file" id="sertifikat_gada_pratama_upload" name="sertifikat_gada_pratama_upload" required accept=".pdf" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                            <p class="text-xs text-muted-foreground mt-1">Format PDF, maksimal 5MB.</p>
                            <x-input-error :messages="$errors->get('sertifikat_gada_pratama_upload')" class="mt-2" />
                        </div>
                    @endif
                    <div>
                        <x-ui.label for="sertifikat_tambahan_upload">Sertifikat Tambahan (opsional)</x-ui.label>
                        <input type="file" id="sertifikat_tambahan_upload" name="sertifikat_tambahan_upload" accept=".pdf" class="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-secondary-foreground hover:file:bg-secondary/80">
                        <p class="text-xs text-muted-foreground mt-1">Format PDF, maksimal 5MB.</p>
                        <x-input-error :messages="$errors->get('sertifikat_tambahan_upload')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-start gap-2.5 rounded-md border bg-muted/40 p-3">
                    <input type="checkbox" id="bersedia_ditempatkan" name="bersedia_ditempatkan" value="1" x-model="fields.bersedia_ditempatkan" required class="mt-0.5 h-4 w-4 shrink-0 rounded border-input text-primary focus:ring-primary">
                    <label for="bersedia_ditempatkan" class="text-sm leading-snug">
                        Dengan ini saya bersedia ditempatkan di unit Al Azhar Wilayah {{ $loker->wilayah ?? $loker->lokasi ?? '-' }}. <span class="text-destructive">*</span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('bersedia_ditempatkan')" class="mt-1" />
            </div>

            <!-- Navigation -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t">
                <x-ui.button type="button" variant="outline" x-show="step > 1" x-cloak @click="back()">
                    &larr; Sebelumnya
                </x-ui.button>
                <span x-show="step === 1"></span>

                <x-ui.button type="button" x-show="step < 3" @click="next()">
                    Selanjutnya &rarr;
                </x-ui.button>
                <x-ui.button type="button" x-show="step === 3" x-cloak @click="openConfirm()">
                    Kirim Lamaran
                </x-ui.button>
            </div>
        </form>

        <!-- Confirmation dialog -->
        <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="confirmOpen = false"></div>
            <div
                x-show="confirmOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="relative w-full max-w-md rounded-lg border bg-background p-6 shadow-lg"
            >
                <h3 class="text-base font-semibold">Konfirmasi Pengiriman Lamaran</h3>
                <p class="mt-1.5 text-sm text-muted-foreground">Mohon periksa kembali data Anda sebelum mengirim. Lamaran tidak dapat diubah setelah dikirim.</p>

                <dl class="mt-4 space-y-1.5 text-sm rounded-md border bg-muted/40 p-3">
                    <div class="flex justify-between gap-2"><dt class="text-muted-foreground">Nama</dt><dd class="font-medium text-right" x-text="fields.nama"></dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-muted-foreground">Email</dt><dd class="font-medium text-right" x-text="fields.email"></dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-muted-foreground">No. WhatsApp</dt><dd class="font-medium text-right" x-text="fields.no_hp"></dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-muted-foreground">Institusi</dt><dd class="font-medium text-right" x-text="fields.institusi"></dd></div>
                </dl>

                <div class="mt-5 flex justify-end gap-2">
                    <x-ui.button type="button" variant="outline" @click="confirmOpen = false">Batal, Periksa Lagi</x-ui.button>
                    <x-ui.button type="button" @click="submitNow()">Ya, Kirim Lamaran</x-ui.button>
                </div>
            </div>
        </div>

        <!-- NIK duplicate alert -->
        <div x-show="nikDuplicateAlertOpen" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="nikDuplicateAlertOpen = false"></div>
            <div
                x-show="nikDuplicateAlertOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="relative w-full max-w-md rounded-lg border bg-background p-6 shadow-lg"
            >
                <h3 class="text-base font-semibold">NIK Sudah Pernah Melamar</h3>
                <p class="mt-1.5 text-sm text-muted-foreground">NIK yang Anda masukkan sudah pernah digunakan untuk melamar lowongan ini sebelumnya. Setiap NIK hanya dapat melamar satu kali untuk lowongan yang sama. Silakan periksa kembali NIK Anda, atau pilih lowongan lain jika ingin melamar posisi berbeda.</p>

                <div class="mt-5 flex justify-end gap-2">
                    <x-ui.button type="button" @click="nikDuplicateAlertOpen = false">Mengerti</x-ui.button>
                </div>
            </div>
        </div>

        <!-- Age limit alert -->
        <div x-show="ageAlertOpen" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="ageAlertOpen = false"></div>
            <div
                x-show="ageAlertOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="relative w-full max-w-md rounded-lg border bg-background p-6 shadow-lg"
            >
                <h3 class="text-base font-semibold">Batas Usia Pelamar</h3>
                <p class="mt-1.5 text-sm text-muted-foreground" x-text="ageAlertMessage"></p>

                <div class="mt-5 flex justify-end gap-2">
                    <x-ui.button type="button" @click="ageAlertOpen = false">Mengerti</x-ui.button>
                </div>
            </div>
        </div>
      </div>
    </x-ui.card>
  </div>

    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('applyWizard', (lokerId, oldInput, errorStep, pendidikanLabels) => ({
                        step: 1,
                        confirmOpen: false,
                        ageAlertOpen: false,
                        nikChecking: false,
                        nikIsDuplicate: false,
                        nikDuplicateAlertOpen: false,
                        draftKey: 'lamaran_draft_' + lokerId,
                        pendidikanLabels: pendidikanLabels ?? {},
                        fields: {
                            nama: oldInput.nama ?? '',
                            nik: oldInput.nik ?? '',
                            tanggal_lahir: oldInput.tanggal_lahir ?? '',
                            jenis_kelamin: oldInput.jenis_kelamin ?? '',
                            email: oldInput.email ?? '',
                            no_hp: oldInput.no_hp ?? '',
                            alamat: oldInput.alamat ?? '',
                            pernah_rekrutmen_sebelumnya: oldInput.pernah_rekrutmen_sebelumnya ?? '',
                            bulan_rekrutmen_sebelumnya: oldInput.bulan_rekrutmen_sebelumnya ?? '',
                            tahun_rekrutmen_sebelumnya: oldInput.tahun_rekrutmen_sebelumnya ?? '',
                            id_tahap_rekrutmen_sebelumnya: oldInput.id_tahap_rekrutmen_sebelumnya ?? '',
                            pernah_bekerja_di_al_azhar: oldInput.pernah_bekerja_di_al_azhar ?? '',
                            lokasi_kerja_al_azhar_sebelumnya: oldInput.lokasi_kerja_al_azhar_sebelumnya ?? '',
                            kerja_al_azhar_periode: oldInput.kerja_al_azhar_periode ?? '',
                            jenis_kepegawaian_al_azhar_sebelumnya: oldInput.jenis_kepegawaian_al_azhar_sebelumnya ?? '',
                            jenis_kepegawaian_al_azhar_lainnya: oldInput.jenis_kepegawaian_al_azhar_lainnya ?? '',
                            id_pendidikan_terakhir: oldInput.id_pendidikan_terakhir ?? '',
                            gelar: oldInput.gelar ?? '',
                            institusi: oldInput.institusi ?? '',
                            program_studi: oldInput.program_studi ?? '',
                            kategori_perguruan_tinggi: oldInput.kategori_perguruan_tinggi ?? '',
                            akreditasi: oldInput.akreditasi ?? '',
                            tahun_lulus: oldInput.tahun_lulus ?? '',
                            ipk_s1: oldInput.ipk_s1 ?? '',
                            ipk_s2: oldInput.ipk_s2 ?? '',
                            ipk_s3: oldInput.ipk_s3 ?? '',
                            ipk_d3: oldInput.ipk_d3 ?? '',
                            bersedia_ditempatkan: !!oldInput.bersedia_ditempatkan,
                        },

                        get isS1() {
                            return this.pendidikanLabels[this.fields.id_pendidikan_terakhir] === 'S1';
                        },

                        get isS2() {
                            return this.pendidikanLabels[this.fields.id_pendidikan_terakhir] === 'S2';
                        },

                        get isS3() {
                            return this.pendidikanLabels[this.fields.id_pendidikan_terakhir] === 'S3';
                        },

                        get isD3() {
                            return this.pendidikanLabels[this.fields.id_pendidikan_terakhir] === 'D3';
                        },

                        get isSekolahMenengah() {
                            const label = this.pendidikanLabels[this.fields.id_pendidikan_terakhir];
                            return label === 'SMP' || label === 'SMA';
                        },

                        init() {
                            // A validation-failure redirect always wins over a stale local draft,
                            // since the server's `old()` input reflects exactly what was just submitted.
                            if (errorStep) {
                                this.step = errorStep;
                            } else {
                                try {
                                    const saved = JSON.parse(localStorage.getItem(this.draftKey) || 'null');
                                    if (saved) {
                                        this.fields = { ...this.fields, ...saved.fields };
                                        this.step = saved.step && saved.step >= 1 && saved.step <= 3 ? saved.step : 1;
                                    }
                                } catch (e) {
                                    // ignore corrupt draft
                                }
                            }

                            this.$watch('fields', () => this.saveDraft(), { deep: true });
                            this.$watch('step', () => this.saveDraft());
                        },

                        saveDraft() {
                            localStorage.setItem(this.draftKey, JSON.stringify({ step: this.step, fields: this.fields }));
                        },

                        clearDraft() {
                            localStorage.removeItem(this.draftKey);
                        },

                        validateStep(n) {
                            const container = this.$refs['step' + n];
                            const inputs = container.querySelectorAll('input, select, textarea');
                            for (const input of inputs) {
                                if (!input.reportValidity()) {
                                    return false;
                                }
                            }
                            return true;
                        },

                        get usia() {
                            if (!this.fields.tanggal_lahir) return null;
                            const dob = new Date(this.fields.tanggal_lahir);
                            if (isNaN(dob)) return null;
                            const today = new Date();
                            let age = today.getFullYear() - dob.getFullYear();
                            const beforeBirthdayThisYear = (today.getMonth() < dob.getMonth())
                                || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate());
                            if (beforeBirthdayThisYear) age--;
                            return age;
                        },

                        get ageAlertMessage() {
                            const batas = this.usia !== null && this.usia < 18
                                ? 'Pelamar dengan usia kurang dari 18 tahun tidak dapat melanjutkan proses pendaftaran ini.'
                                : 'Pelamar dengan usia lebih dari 35 tahun tidak dapat melanjutkan proses pendaftaran ini.';
                            return 'Maaf, usia Anda saat ini adalah ' + this.usia + ' tahun. ' + batas;
                        },

                        checkAge() {
                            if (this.usia !== null && (this.usia < 18 || this.usia > 35)) {
                                this.ageAlertOpen = true;
                            }
                        },

                        async checkNik() {
                            const nik = this.fields.nik;
                            if (!/^\d{16}$/.test(nik)) {
                                this.nikIsDuplicate = false;
                                return;
                            }
                            this.nikChecking = true;
                            try {
                                const res = await fetch(`/loker/${lokerId}/cek-nik?nik=${encodeURIComponent(nik)}`, {
                                    headers: { 'Accept': 'application/json' },
                                });
                                const data = await res.json();
                                this.nikIsDuplicate = !!data.sudah_melamar;
                                if (this.nikIsDuplicate) {
                                    this.nikDuplicateAlertOpen = true;
                                }
                            } catch (e) {
                                // Network error: let server-side validation on submit catch it instead.
                            } finally {
                                this.nikChecking = false;
                            }
                        },

                        next() {
                            if (!this.validateStep(this.step)) return;
                            if (this.step === 1 && this.usia !== null && (this.usia < 18 || this.usia > 35)) {
                                this.ageAlertOpen = true;
                                return;
                            }
                            if (this.step === 1 && this.nikIsDuplicate) {
                                this.nikDuplicateAlertOpen = true;
                                return;
                            }
                            if (this.step < 3) this.step++;
                            this.$refs.form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        },

                        back() {
                            if (this.step > 1) this.step--;
                        },

                        openConfirm() {
                            if (!this.validateStep(3)) return;
                            this.confirmOpen = true;
                        },

                        submitNow() {
                            this.clearDraft();
                            this.confirmOpen = false;
                            this.$refs.form.submit();
                        },
                    }));
                });
            </script>
        @endpush
    @endonce
</x-layouts.public>
