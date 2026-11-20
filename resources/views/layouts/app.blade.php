<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|plus-jakarta-sans:500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-muted/40 lg:flex" x-data="{ sidebarOpen: false }">
            <!-- Mobile sidebar backdrop -->
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
                    x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-linear duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-40 bg-black/30 lg:hidden"></div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                    class="fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col border-r bg-background shadow-lg transition-transform duration-200 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-none">
                <a href="{{ route('admin.dashboard') }}" class="flex h-16 items-center gap-2.5 border-b px-5 shrink-0">
                    <x-application-logo class="h-7 w-7 fill-current text-foreground shrink-0" />
                    <span class="leading-tight">
                        <span class="block font-semibold text-sm tracking-tight text-foreground">Al Azhar Career</span>
                        <span class="block text-xs text-muted-foreground">Portal Rekrutmen</span>
                    </span>
                </a>
                <div class="flex-1 overflow-y-auto px-3 py-4">
                    @include('layouts.sidebar')
                </div>
            </aside>

            <!-- Main column -->
            <div class="flex min-w-0 flex-1 flex-col">
                @include('layouts.navigation')

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>

                <footer class="border-t bg-background p-3 text-center text-xs text-muted-foreground">
                    &copy;2026 Direktorat IT dan Transformasi Digital YPIA. All rights reserved.
                </footer>
            </div>
        </div>

        <!-- Floating horizontal slider for wide tables (toggled from the sidebar) -->
        <div x-data="floatingTableSlider" x-show="$store.tableSlider.on && target" x-cloak
                class="fixed bottom-4 z-30 flex items-center gap-2 rounded-full border bg-background/95 px-3 py-2 shadow-lg backdrop-blur"
                :style="`left: ${left}px; width: ${width}px`">
            <svg class="h-4 w-4 shrink-0 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
            </svg>
            <input type="range" min="0" :max="max" step="1" :value="value" @input="scrollTo($event.target.value)"
                    aria-label="Geser tabel ke kiri/kanan" class="h-2 w-full cursor-pointer accent-primary">
        </div>

        <x-ui.toaster />
        <x-ui.alert-dialog />
    </body>
</html>
