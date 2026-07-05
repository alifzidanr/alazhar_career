@props(['title' => null, 'description' => null, 'padded' => true])

<div {{ $attributes->merge(['class' => 'rounded-lg border bg-card text-card-foreground shadow-sm']) }}>
    @if ($title || $description || isset($header))
        <div class="flex flex-col space-y-1.5 p-6 border-b">
            @isset($header)
                {{ $header }}
            @else
                @if ($title)
                    <h3 class="font-semibold leading-none tracking-tight">{{ $title }}</h3>
                @endif
                @if ($description)
                    <p class="text-sm text-muted-foreground">{{ $description }}</p>
                @endif
            @endisset
        </div>
    @endif

    <div class="{{ $padded ? 'p-6' : '' }}">
        {{ $slot }}
    </div>
</div>
