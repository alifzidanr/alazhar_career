@props(['tahap'])

<x-ui.badge variant="secondary" {{ $attributes }}>
    {{ $tahap->id_tahap_rekrutmen }}. {{ $tahap->tahap_rekrutmen }}
</x-ui.badge>
