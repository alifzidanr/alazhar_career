@props(['variant' => 'default'])

@php
    $variants = [
        'default' => 'bg-background text-foreground border-border',
        'destructive' => 'border-destructive/50 text-destructive bg-destructive/10 [&>svg]:text-destructive',
        'success' => 'border-emerald-200 text-emerald-800 bg-emerald-50',
        'warning' => 'border-amber-200 text-amber-800 bg-amber-50',
    ];

    $classes = 'relative w-full rounded-lg border px-4 py-3 text-sm '.($variants[$variant] ?? $variants['default']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    {{ $slot }}
</div>
