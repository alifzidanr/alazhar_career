<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-foreground antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-muted/40">
            <div class="flex items-center gap-2">
                <x-application-logo class="w-9 h-9 fill-current text-foreground" />
                <span class="font-semibold text-lg tracking-tight">Al Azhar Career</span>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-card text-card-foreground border rounded-lg shadow-sm">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
