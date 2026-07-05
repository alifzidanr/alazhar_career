@props(['status'])

@php
    $variants = [
        1 => 'success', // lolos
        2 => 'muted', // tidak lolos
        3 => 'warning', // dicadangkan
        4 => 'destructive', // ditolak
        5 => 'outline', // mundur
        6 => 'info', // screening
    ];
    $variant = $variants[$status->id_status_pelamar] ?? 'secondary';
@endphp

<x-ui.badge :variant="$variant" {{ $attributes }}>
    {{ ucfirst($status->status_pelamar) }}
</x-ui.badge>
