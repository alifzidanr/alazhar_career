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
        <x-ui.input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi', $loker->lokasi ?? '') }}" />
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
