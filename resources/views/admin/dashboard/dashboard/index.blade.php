<x-admin.layouts.auth title="Dashboard">
<x-slot:header>
        Dashboard
    </x-slot:header>
    <div class="p-6 sm:p-8">
        <x-admin.card>
            <x-slot:header>
                <x-slot:title>
                    Dashboard
                </x-slot:title>
                <x-slot:description>
                    All statistics related to your account.
                </x-slot:description>
            </x-slot:header>
            <x-slot:content>
                {{ __("You're logged in") }}
            </x-slot:content>
        </x-admin.card>
    </div>
</x-admin.layouts.auth>
