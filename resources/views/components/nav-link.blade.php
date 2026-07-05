@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium bg-accent text-accent-foreground transition-colors'
            : 'inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-accent/50 transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
