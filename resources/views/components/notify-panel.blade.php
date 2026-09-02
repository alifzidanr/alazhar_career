@props(['pelamar'])

@php
    $templates = \App\Support\NotifikasiTemplates::all();
    $suggested = \App\Support\NotifikasiTemplates::suggestedKey($pelamar);
    $rendered = collect($templates)->mapWithKeys(fn ($t, $key) => [$key => \App\Support\NotifikasiTemplates::render($key, $pelamar)]);
@endphp

<div x-data="{
    template: '{{ $suggested ?? '' }}',
    subject: '',
    body: '',
    templates: {{ $rendered->toJson() }},
    applyTemplate() {
        if (this.template && this.templates[this.template]) {
            this.subject = this.templates[this.template].subject;
            this.body = this.templates[this.template].body;
        } else {
            this.subject = '';
            this.body = '';
        }
    }
}" x-init="applyTemplate()">
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="space-y-3">
            <div>
                <x-ui.label for="notify-template">Template Pesan</x-ui.label>
                <x-ui.select id="notify-template" x-model="template" @change="applyTemplate()">
                    <option value="">-- Kosong / Tulis Manual --</option>
                    @foreach ($templates as $key => $t)
                        <option value="{{ $key }}">{{ $t['label'] }}</option>
                    @endforeach
                </x-ui.select>
            </div>

            <div>
                <x-ui.label for="notify-subject">Subjek (email)</x-ui.label>
                <x-ui.input id="notify-subject" type="text" x-model="subject" />
            </div>
        </div>

        <div>
            <x-ui.label for="notify-body">Isi Pesan</x-ui.label>
            <x-ui.textarea id="notify-body" x-model="body" rows="6" class="h-[calc(100%-1.75rem)]"></x-ui.textarea>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.pelamar.notify', $pelamar) }}" class="mt-4 flex gap-2">
        @csrf
        <input type="hidden" name="template" :value="template">
        <input type="hidden" name="subject" :value="subject">
        <input type="hidden" name="body" :value="body">
        <input type="hidden" name="channel" value="email">

        <x-ui.button type="submit" @click.prevent="$dispatch('confirm-dialog', { title: 'Kirim email ini ke {{ $pelamar->email }}?', form: $el.closest('form') })"
            class="bg-blue-600 text-white shadow-sm hover:bg-blue-500"
            :disabled="! $pelamar->email">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M2.25 6.75A2.25 2.25 0 0 1 4.5 4.5h15a2.25 2.25 0 0 1 2.25 2.25v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75Zm2.4-.75 7.35 5.51L19.35 6H4.65Zm15.6 1.29-7.03 5.27a1.5 1.5 0 0 1-1.79 0L4.35 7.29V17.25h15.15V7.29Z"/></svg>
            Kirim via Email
        </x-ui.button>
    </form>
</div>
