<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Rekrutmen YPI Al Azhar' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-muted/40 text-foreground" x-data="{ mobileNavOpen: false }">
    <header class="bg-background border-b sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('loker.index') }}" class="flex items-center gap-2 font-bold tracking-tight shrink-0">
                    <x-application-logo class="h-7 w-7 fill-current text-foreground" />
                    <span class="hidden sm:inline">Rekrutmen YPI Al Azhar</span>
                    <span class="sm:hidden">YPI Al Azhar</span>
                </a>

                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('loker.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('loker.index') ? 'text-foreground' : 'text-muted-foreground hover:text-foreground' }}">Beranda</a>
                    <a href="{{ route('loker.list') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('loker.list', 'loker.show') ? 'text-foreground' : 'text-muted-foreground hover:text-foreground' }}">Lowongan</a>
                    <a href="{{ route('status.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('status.*') ? 'text-foreground' : 'text-muted-foreground hover:text-foreground' }}">Status Lamaran</a>
                    <a href="{{ route('tentang.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('tentang.index') ? 'text-foreground' : 'text-muted-foreground hover:text-foreground' }}">Tentang Kami</a>
                </nav>

                <div class="flex items-center gap-2">
                    <x-ui.button :href="auth()->check() ? route('admin.pelamar.index') : route('login')" size="sm" class="hidden sm:inline-flex">{{ auth()->check() ? 'Panel Admin' : 'Masuk' }}</x-ui.button>
                    <button @click="mobileNavOpen = !mobileNavOpen" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-muted-foreground hover:bg-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path x-show="!mobileNavOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            <path x-show="mobileNavOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <nav x-show="mobileNavOpen" x-cloak class="md:hidden pb-4 flex flex-col gap-1">
                <a href="{{ route('loker.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('loker.index') ? 'bg-accent text-foreground' : 'text-muted-foreground' }}">Beranda</a>
                <a href="{{ route('loker.list') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('loker.list', 'loker.show') ? 'bg-accent text-foreground' : 'text-muted-foreground' }}">Lowongan</a>
                <a href="{{ route('status.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('status.*') ? 'bg-accent text-foreground' : 'text-muted-foreground' }}">Status Lamaran</a>
                <a href="{{ route('tentang.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('tentang.index') ? 'bg-accent text-foreground' : 'text-muted-foreground' }}">Tentang Kami</a>
                <x-ui.button :href="auth()->check() ? route('admin.pelamar.index') : route('login')" size="sm" class="mt-2">{{ auth()->check() ? 'Panel Admin' : 'Masuk' }}</x-ui.button>
            </nav>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="mt-20 border-t bg-background">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <a href="{{ route('loker.index') }}" class="flex items-center gap-2 font-bold tracking-tight">
                        <x-application-logo class="h-6 w-6 fill-current text-foreground" />
                        Rekrutmen YPI Al Azhar
                    </a>
                    <p class="mt-3 text-sm text-muted-foreground leading-relaxed">
                        Mewujudkan standar keunggulan pendidikan Islam di Indonesia melalui seleksi yang profesional serta pengembangan talenta terbaik.
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold">Tentang Rekrutmen</h3>
                    <div class="mt-2 mb-4 border-t"></div>
                    <ul class="space-y-2 text-sm text-muted-foreground">
                        <li><a href="#" class="hover:text-foreground">Panduan Melamar</a></li>
                        <li><a href="#" class="hover:text-foreground">Tahapan Seleksi</a></li>
                        <li><a href="#" class="hover:text-foreground">Syarat &amp; Ketentuan</a></li>
                        <li><a href="#" class="hover:text-foreground">Pertanyaan Umum</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold">Tautan Cepat</h3>
                    <div class="mt-2 mb-4 border-t"></div>
                    <ul class="space-y-2 text-sm text-muted-foreground">
                        <li><a href="#" class="hover:text-foreground">Unit Pendidikan</a></li>
                        <li><a href="#" class="hover:text-foreground">Profil Yayasan</a></li>
                        <li><a href="#" class="hover:text-foreground">Hubungi Kami</a></li>
                        <li><a href="#" class="hover:text-foreground">Pusat Bantuan</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold">Alamat Pusat</h3>
                    <div class="mt-2 mb-4 border-t"></div>
                    <p class="text-sm text-muted-foreground leading-relaxed">
                        Yayasan Pendidikan Islam Al Azhar<br>
                        Jl. Sisingamangaraja No. 1, Kebayoran Baru, Jakarta Selatan, 12110
                    </p>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t text-sm text-muted-foreground">
                &copy; {{ date('Y') }} Yayasan Pendidikan Islam Al-Azhar
            </div>
        </div>
    </footer>

    <x-ui.toaster />

    @stack('scripts')
</body>
</html>
