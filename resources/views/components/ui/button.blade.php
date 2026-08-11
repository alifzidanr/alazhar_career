@props([
    'variant' => 'default',
    'size' => 'default',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';

    $variants = [
        'default' => 'bg-primary text-primary-foreground shadow hover:bg-primary/90',
        'destructive' => 'bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90',
        'outline' => 'border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground',
        'secondary' => 'bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80',
        'ghost' => 'hover:bg-accent hover:text-accent-foreground',
        'link' => 'text-primary underline-offset-4 hover:underline',
        'brand' => 'bg-brand-green-500 text-white shadow hover:bg-brand-green-600',
        'navy' => 'bg-brand-navy-600 text-white shadow hover:bg-brand-navy-700',
        'outline-invert' => 'border border-white/40 bg-white/10 text-white backdrop-blur-sm hover:bg-white/20',
    ];

    $sizes = [
        'default' => 'h-9 px-4 py-2',
        'sm' => 'h-8 rounded-md px-3 text-xs',
        'lg' => 'h-10 rounded-md px-8',
        'icon' => 'h-9 w-9',
    ];

    $classes = $base.' '.($variants[$variant] ?? $variants['default']).' '.($sizes[$size] ?? $sizes['default']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if ($disabled) aria-disabled="true" tabindex="-1" onclick="return false;" @endif>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
