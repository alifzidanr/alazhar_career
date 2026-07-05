@props(['value'])

<x-ui.label {{ $attributes }}>
    {{ $value ?? $slot }}
</x-ui.label>
