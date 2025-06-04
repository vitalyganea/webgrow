<x-admin.layouts.auth title="Security">
    <x-slot:header>
        Security Information
    </x-slot:header>
    <x-admin.settings-tabs class="max-w-xl">
        <x-admin.card>
            <x-slot:header>
                <x-slot:title>Update Password</x-slot:title>
                <x-slot:description>Ensure your account is using a long, random password to stay secure.</x-slot:description>
            </x-slot:header>
            <x-slot:content>
                <form action="{{ route('admin.settings.security') }}" method="post">
                    @csrf
                    @method('put')
                    <div>
                        <x-admin.label for="current_password" :value="__('current password')" />
                        <x-admin.input class="mt-1" id="current_password" name="current_password" type="password" autocomplete="current_password" />
                        <x-admin.input-error class="mt-1" :messages="$errors->updatePassword->get('current_password')" />
                    </div>
                    <div class="mt-6">
                        <x-admin.label for="password" :value="__('new password')" />
                        <x-admin.input class="mt-1" id="password" name="password" type="password" autocomplete="new-password" />
                        <x-admin.input-error class="mt-2" :messages="$errors->updatePassword->get('password')" />
                    </div>
                    <div class="mt-6">
                        <x-admin.label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-admin.input class="mt-1" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                        <x-admin.input-error class="mt-2" :messages="$errors->updatePassword->get('password_confirmation')" />
                    </div>
                    <div class="mt-6 flex items-center gap-4">
                        <x-admin.button>{{ __('Save') }}</x-admin.button>
                        <x-admin.action-message status="password-updated" />
                    </div>
                </form>
            </x-slot:content>
        </x-admin.card>
    </x-admin.settings-tabs>
</x-admin.layouts.auth>
