<x-tabs>
    <x-slot:header>
        <x-tabs.link value="Account" :href="route('admin.settings.account')" :active="request()->routeIs('admin.settings.account')" />
        <x-tabs.link value="Security" :href="route('admin.settings.security')" :active="request()->routeIs('admin.settings.security')" />
        <x-tabs.link value="Danger" :href="route('admin.settings.danger')" :active="request()->routeIs('admin.settings.danger')" />
    </x-slot:header>
    <x-slot:content>
        <div {{ $attributes->twMerge('') }}>{{ $slot }}</div>
    </x-slot:content>
</x-tabs>
