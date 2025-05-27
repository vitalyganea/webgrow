<nav class='hidden border-b bg-card py-6 lg:block'>
    <div class="mx-auto flex max-w-screen-2xl items-center justify-between px-4 sm:px-6">
        <div class="flex items-center gap-x-6">
            <a href="{{ route('home') }}">
                <x-application-logo class="block h-12 w-auto fill-muted-foreground transition duration-300 hover:fill-foreground" />
            </a>
            <div class="flex items-center gap-x-3">
                <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Home') }}
                </x-nav-link>
                <x-nav-link :href="route('articles')" :active="request()->routeIs('articles')">
                    {{ __('Articles') }}
                </x-nav-link>
                <x-nav-link :href="route('series')" :active="request()->routeIs('series')">
                    {{ __('Series') }}
                </x-nav-link>
            </div>
        </div>
    </div>
</nav>
