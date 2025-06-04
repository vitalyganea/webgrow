<x-base-layout>
    <div class="min-h-screen">
        <x-public.navigations />
        <x-public.side-navigation />
        @isset($header)
            <header class="border-b py-6 shadow-sm sm:py-8 lg:py-12">
                <x-public.container>
                    <h1 class="text-xl font-semibold text-foreground">
                        {{ $header }}
                    </h1>
                </x-public.container>
            </header>
        @endisset
        <main class="py-3">
            {{ $slot }}
        </main>
    </div>
</x-base-layout>
