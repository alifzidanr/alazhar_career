@php($loker = $loker ?? null)

<div class="grid sm:grid-cols-2 gap-5">
    <div class="sm:col-span-2">
        <x-ui.label for="judul_loker">Judul Loker <span class="text-destructive">*</span></x-ui.label>
        <x-ui.input type="text" id="judul_loker" name="judul_loker" value="{{ old('judul_loker', $loker->judul_loker ?? '') }}" required />
    </div>

    <div class="sm:col-span-2">
        <x-ui.label for="deskripsi_loker">Deskripsi</x-ui.label>
        <x-ui.textarea id="deskripsi_loker" name="deskripsi_loker" rows="4">{{ old('deskripsi_loker', $loker->deskripsi_loker ?? '') }}</x-ui.textarea>
    </div>

    <div>
        <x-ui.label for="lokasi">Lokasi</x-ui.label>
        @php($lokasiTerpilih = old('lokasi', $loker->lokasi ?? ''))
        <x-ui.select id="lokasi" name="lokasi">
            <option value="">-- Pilih Lokasi --</option>
            @foreach ($lokasiList as $l)
                <option value="{{ $l->nama_lokasi }}" @selected($lokasiTerpilih === $l->nama_lokasi)>{{ $l->nama_lokasi }}</option>
            @endforeach
            @if ($lokasiTerpilih !== '' && ! $lokasiList->contains('nama_lokasi', $lokasiTerpilih))
                <option value="{{ $lokasiTerpilih }}" selected>{{ $lokasiTerpilih }}</option>
            @endif
        </x-ui.select>
        <p class="text-xs text-muted-foreground mt-1">Belum ada pilihan yang cocok? Tambahkan lewat menu <a href="{{ route('admin.lokasi.index') }}" class="underline">Lokasi</a>.</p>
    </div>

    <div>
        <x-ui.label for="id_pendidikan_terakhir">Pendidikan Minimum <span class="text-destructive">*</span></x-ui.label>
        <x-ui.select id="id_pendidikan_terakhir" name="id_pendidikan_terakhir" required>
            <option value="">-- Pilih --</option>
            @foreach ($pendidikanList as $p)
                <option value="{{ $p->id_pendidikan_terakhir }}" @selected((string) old('id_pendidikan_terakhir', $loker->id_pendidikan_terakhir ?? '') === (string) $p->id_pendidikan_terakhir)>{{ $p->pendidikan_terakhir }}</option>
            @endforeach
        </x-ui.select>
    </div>

    <div>
        <x-ui.label for="id_jenjang">Jenjang <span class="text-destructive">*</span></x-ui.label>
        <x-ui.select id="id_jenjang" name="id_jenjang" required>
            <option value="">-- Pilih --</option>
            @foreach ($jenjangList as $j)
                <option value="{{ $j->id_jenjang }}" @selected((string) old('id_jenjang', $loker->id_jenjang ?? '') === (string) $j->id_jenjang)>{{ $j->nama_jenjang }}</option>
            @endforeach
        </x-ui.select>
        <p class="text-xs text-muted-foreground mt-1">Belum ada pilihan yang cocok? Tambahkan lewat menu <a href="{{ route('admin.jenjang.index') }}" class="underline">Jenjang</a>.</p>
    </div>

    <div>
        <x-ui.label for="status_loker">Status <span class="text-destructive">*</span></x-ui.label>
        <x-ui.select id="status_loker" name="status_loker" required>
            <option value="dibuka" @selected(old('status_loker', $loker->status_loker ?? 'dibuka') === 'dibuka')>Dibuka</option>
            <option value="ditutup" @selected(old('status_loker', $loker->status_loker ?? '') === 'ditutup')>Ditutup</option>
        </x-ui.select>
    </div>

    <div>
        <x-ui.label for="start_time">Mulai</x-ui.label>
        <x-ui.input type="date" id="start_time" name="start_time" value="{{ old('start_time', optional($loker->start_time ?? null)->format('Y-m-d')) }}" />
    </div>

    <div>
        <x-ui.label for="end_time">Berlaku Sampai</x-ui.label>
        <x-ui.input type="date" id="end_time" name="end_time" value="{{ old('end_time', optional($loker->end_time ?? null)->format('Y-m-d')) }}" />
    </div>
</div>
