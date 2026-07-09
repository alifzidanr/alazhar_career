@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium bg-accent text-accent-foreground transition-colors'
            : 'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-accent/50 transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
