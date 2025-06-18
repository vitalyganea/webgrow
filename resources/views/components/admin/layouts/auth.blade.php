<x-admin.index title="Dashboard">
    <div class="min-h-screen relative" x-data="{ mobileMenuOpen: false }">
        <!-- Fixed desktop theme toggle -->
        <div class="fixed bottom-0 left-0 mb-2 ml-4 hidden lg:block z-10">
            <x-admin.button id="theme-toggle-desktop" type="button" class="text-xl theme-toggle h-[2.7rem] w-[2.7rem]" size="icon" variant="toggle">
                <i class="fas fa-moon hidden" id="theme-toggle-desktop-light-icon"></i>
                <i class="fas fa-sun hidden" id="theme-toggle-desktop-dark-icon"></i>
            </x-admin.button>
        </div>

        <!-- Mobile menu overlay -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-black bg-opacity-25 lg:hidden"
             @click="mobileMenuOpen = false">
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 z-50 w-80 bg-background border-r shadow-lg lg:hidden">
            <div class="flex h-full flex-col">
                <!-- Mobile menu header -->
                <div class="flex items-center justify-between border-b p-4">
                    <h2 class="text-lg font-semibold">Menu</h2>
                    <button @click="mobileMenuOpen = false"
                            class="rounded-md p-2 text-muted-foreground hover:bg-accent hover:text-accent-foreground">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Mobile menu content -->
                <div class="flex-1 overflow-y-auto p-4">
                    <ul class="flex w-full flex-col gap-y-2">
                        <!-- User info -->
                        <li class="mb-8">
                            <div class="flex items-center font-normal">
                                <div class="mr-3 shrink-0">
                                    <x-admin.avatar class="h-12 w-12">
                                        <x-admin.avatar.image :src="asset('storage/' . auth()->user()->avatar())" />
                                        <x-admin.avatar.fallback :value="acronym(auth()->user()->name)" />
                                    </x-admin.avatar>
                                </div>
                                <div>
                                    <div>{{ auth()->user()->name }}</div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ auth()->user()->email }}
                                    </div>
                                </div>
                            </div>
                        </li>
                        @foreach(config('admin.admin-sidebar') as $item)
                            <x-admin.aside.link
                                :href="route($item['route'])"
                                :active="request()->routeIs($item['active_routes'])"
                                @click="mobileMenuOpen = false">
                                <i class="{{$item['icon']}} mr-2"></i>
                                {{$item['label']}}
                            </x-admin.aside.link>
                        @endforeach
                        <x-admin.separator />

                        <!-- Theme toggle for mobile -->
                        <li class="py-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium">Theme</span>
                                <x-admin.button id="theme-toggle-mobile" type="button" class="theme-toggle h-[2.7rem] w-[2.7rem]" size="icon" variant="toggle">
                                    <i class="fas fa-moon hidden h-5 w-5" id="theme-toggle-mobile-light-icon"></i>
                                    <i class="fas fa-sun hidden h-5 w-5" id="theme-toggle-mobile-dark-icon"></i>
                                </x-admin.button>
                            </div>
                        </li>

                        <x-admin.separator />

                        <!-- Logout -->
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <x-admin.aside.link :href="route('admin.logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                {{ __('Log Out') }}
                            </x-admin.aside.link>
                        </form>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Rest of the template remains unchanged -->
        <div class="mx-auto flex max-w-screen-2xl">
            <aside class="hidden min-h-screen w-30 shrink-0 items-start border-r p-8 lg:flex">
                <ul class="sticky top-8 flex w-full flex-col gap-y-2">
                    <li class="mb-8">
                        <div class="flex items-center font-normal">
                            <div class="mr-3 shrink-0">
                                <x-admin.avatar class="h-12 w-12">
                                    <x-admin.avatar.image :src="asset('storage/' . auth()->user()->avatar())" />
                                    <x-admin.avatar.fallback :value="acronym(auth()->user()->name)" />
                                </x-admin.avatar>
                            </div>
                            <div>
                                <div>{{ auth()->user()->name }}</div>
                                <div class="text-sm text-muted-foreground">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>
                        </div>
                    </li>
                    @foreach(config('admin.admin-sidebar') as $item)
                        <x-admin.aside.link
                            :href="route($item['route'])"
                            :active="request()->routeIs($item['active_routes'])">
                            <i class="{{$item['icon']}} mr-2"></i>
                            {{$item['label']}}
                        </x-admin.aside.link>
                    @endforeach
                    <x-admin.separator />

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <x-admin.aside.link :href="route('admin.logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            {{ __('Log Out') }}
                        </x-admin.aside.link>
                    </form>
                </ul>
            </aside>

            <main class="w-full">
                @isset($header)
                    <header class="flex items-center justify-between border-b border-r bg-card px-6 py-4 sm:px-8 sm:py-6">
                        <button @click="mobileMenuOpen = true"
                                class="rounded-md p-2 text-muted-foreground hover:bg-accent hover:text-accent-foreground lg:hidden">
                            <i class="fas fa-bars text-lg"></i>
                        </button>

                        <h1 class="text-lg font-semibold text-foreground">
                            {{ $header }}
                        </h1>

                        <div class="w-10 lg:hidden"></div>
                    </header>
                @endisset
                <div>
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</x-admin.index>
