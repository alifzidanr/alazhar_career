<nav class="bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/80 border-b sticky top-0 z-30">
    <div class="flex h-16 items-center gap-4 px-4 sm:px-6">
        <!-- Sidebar toggle (mobile) -->
        <button @click="sidebarOpen = ! sidebarOpen" class="lg:hidden inline-flex items-center justify-center p-2 -ms-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-accent focus:outline-none transition-colors">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Page Heading -->
        @isset($header)
            <div class="min-w-0 flex-1">{{ $header }}</div>
        @else
            <div class="flex-1"></div>
        @endisset

        <!-- Profile Dropdown -->
        <div class="flex items-center shrink-0">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium text-foreground hover:bg-accent transition-colors focus:outline-none">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
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
    </div>
</nav>
