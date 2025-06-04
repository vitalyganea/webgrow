<x-admin.layouts.guest title="Register">
<x-admin.card>
        <x-slot:header>
            <x-slot:title>Register</x-slot:title>
            <x-slot:description>Hi, feel free to register</x-slot:description>
        </x-slot:header>
        <x-slot:content>
            <form method="POST" action="{{ route('admin.register') }}">
                @csrf

                <!-- Name -->
                <div>
                    <x-admin.label for="name" :value="__('Name')" />
                    <x-admin.input class="mt-1 block w-full" id="name" name="name" type="text" :value="old('name')" required autofocus autocomplete="name" />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-admin.label for="email" :value="__('Email')" />
                    <x-admin.input class="mt-1 block w-full" id="email" name="email" type="email" :value="old('email')" required autocomplete="username" />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-admin.label for="password" :value="__('Password')" />

                    <x-admin.input class="mt-1 block w-full" id="password" name="password" type="password" required autocomplete="new-password" />

                    <x-admin.input-error class="mt-2" :messages="$errors->get('password')" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-admin.label for="password_confirmation" :value="__('Confirm Password')" />

                    <x-admin.input class="mt-1 block w-full" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" />

                    <x-admin.input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <a class="text-sm text-muted-foreground hover:text-foreground" href="{{ route('admin.login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <x-admin.button class="ml-4">
                        {{ __('Register') }}
                    </x-admin.button>
                </div>
            </form>
        </x-slot:content>
    </x-admin.card>
</x-admin.layouts.guest>
