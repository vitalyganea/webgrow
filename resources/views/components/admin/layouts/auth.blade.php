<x-admin.index title="Dashboard">
    <div class="min-h-screen">
        <x-admin.side-navigation />
        <div class="fixed bottom-0 left-0 mb-2 ml-4 hidden lg:block">
            <x-admin.theme-toggle class="hidden h-[2.7rem] w-[2.7rem] lg:block" size="icon" variant="toggle" />
        </div>
        <div class="mx-auto flex max-w-screen-2xl">
            <x-admin.aside>
                <li class="mb-8">
                    <div class="flex items-center font-normal">
                        <div class="mr-3 shrink-0">
                            <x-admin.avatar class="h-12 w-12">
                                <x-admin.avatar.image :src="auth()->user()->avatar()" />
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
                <x-admin.aside.link :href="route('admin.get.pages')">
                    <x-tabler-home />
                    Pages
                </x-admin.aside.link>
                <x-admin.aside.link :href="route('admin.get.languages')">
                    <x-tabler-language />
                    Languages
                </x-admin.aside.link>
                <x-admin.aside.link :href="route('admin.get.seo-tags')">
                    <x-tabler-seo />
                    Seo Tags
                </x-admin.aside.link>
                <x-admin.aside.link :href="route('admin.settings.account')" :active="request()->routeIs('admin.settings.*')">
                    <x-tabler-settings />
                    Settings
                </x-admin.aside.link>
                <x-admin.separator />
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <x-admin.aside.link :href="route('admin.logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        <x-tabler-logout />
                        {{ __('Log Out') }}
                    </x-admin.aside.link>
                </form>
            </x-admin.aside>
            <main class="w-full">
                @isset($header)
                    <header class="flex items-center justify-between border-b bg-card px-6 py-4 sm:px-8 sm:py-6">
                        <h1 class="text-lg font-semibold text-foreground">
                            {{ $header }}
                        </h1>
                    </header>
                @endisset
                <div>
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- Add SweetAlert2 and Animate.css CDNs -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Success flash message
            @if (session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                animation: false,
                customClass: {
                    popup: 'animate__animated animate__slideInRight',
                    container: 'swal2-container-high-zindex'
                },
                didOpen: () => {
                    setTimeout(() => {
                        Swal.getPopup().classList.remove('animate__slideInRight');
                        Swal.getPopup().classList.add('animate__slideOutRight');
                    }, 2500);
                }
            });
            @endif

            // Error flash message
            @if (session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                animation: false,
                customClass: {
                    popup: 'animate__animated animate__slideInRight',
                    container: 'swal2-container-high-zindex'
                },
                didOpen: () => {
                    setTimeout(() => {
                        Swal.getPopup().classList.remove('animate__slideInRight');
                        Swal.getPopup().classList.add('animate__slideOutRight');
                    }, 2500);
                }
            });
            @endif
        });
    </script>
</x-admin.index>
