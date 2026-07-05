@props(['variant' => 'default'])

@php
    $variants = [
        'default' => 'border-transparent bg-primary text-primary-foreground hover:bg-primary/80',
        'secondary' => 'border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80',
        'destructive' => 'border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80',
        'outline' => 'text-foreground border-border',
        'success' => 'border-transparent bg-emerald-100 text-emerald-800 hover:bg-emerald-100/80',
        'warning' => 'border-transparent bg-amber-100 text-amber-800 hover:bg-amber-100/80',
        'info' => 'border-transparent bg-blue-100 text-blue-800 hover:bg-blue-100/80',
        'muted' => 'border-transparent bg-muted text-muted-foreground hover:bg-muted/80',
    ];

    $classes = 'inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 '.($variants[$variant] ?? $variants['default']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
