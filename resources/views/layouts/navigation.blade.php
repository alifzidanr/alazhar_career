<nav x-data="{ open: false }" class="bg-background border-b">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14 items-center">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.lamaran.index') }}" class="flex items-center gap-2 font-semibold text-sm tracking-tight">
                        <x-application-logo class="h-6 w-6 fill-current text-foreground" />
                        Al Azhar Career
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:ms-8 sm:flex">
                    <x-nav-link :href="route('admin.lamaran.index')" :active="request()->routeIs('admin.lamaran.*')">
                        {{ __('Daftar Lamaran') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.pelamar.index')" :active="request()->routeIs('admin.pelamar.*')">
                        {{ __('Manajemen Pelamar') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.loker.index')" :active="request()->routeIs('admin.loker.*')">
                        {{ __('Manajemen Loker') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.kriteria.index')" :active="request()->routeIs('admin.kriteria.*')">
                        {{ __('Kriteria') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium text-foreground hover:bg-accent transition-colors focus:outline-none">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('admin.profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <a href="{{ route('loker.index') }}" target="_blank" class="block w-full mx-1 px-3 py-1.5 text-start text-sm text-popover-foreground hover:bg-accent hover:text-accent-foreground focus:outline-none focus:bg-accent transition-colors rounded-sm" style="width: calc(100% - 0.5rem);">
                            {{ __('Lihat Portal Publik') }}
                        </a>

                        <div class="my-1 border-t"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-accent focus:outline-none transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.lamaran.index')" :active="request()->routeIs('admin.lamaran.*')">
                {{ __('Daftar Lamaran') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.pelamar.index')" :active="request()->routeIs('admin.pelamar.*')">
                {{ __('Manajemen Pelamar') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.loker.index')" :active="request()->routeIs('admin.loker.*')">
                {{ __('Manajemen Loker') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.kriteria.index')" :active="request()->routeIs('admin.kriteria.*')">
                {{ __('Kriteria') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t">
            <div class="px-4">
                <div class="font-medium text-base text-foreground">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-muted-foreground">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('admin.profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
