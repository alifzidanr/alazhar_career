@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-2 border-foreground text-start text-base font-medium text-foreground bg-accent transition-colors'
            : 'block w-full ps-3 pe-4 py-2 border-l-2 border-transparent text-start text-base font-medium text-muted-foreground hover:text-foreground hover:bg-accent/50 transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
