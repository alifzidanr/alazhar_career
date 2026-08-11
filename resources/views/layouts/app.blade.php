<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
                    class="fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col border-r bg-background shadow-lg transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 lg:shadow-none">
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

        <x-ui.toaster />
        <x-ui.alert-dialog />
    </body>
</html>
