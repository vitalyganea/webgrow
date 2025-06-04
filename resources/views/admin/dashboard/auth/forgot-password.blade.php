<x-admin.layouts.guest>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-admin.auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-admin.label for="email" :value="__('Email')" />
            <x-admin.input class="mt-1 block w-full" id="email" name="email" type="email" :value="old('email')" required autofocus />
            <x-admin.input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <x-admin.button>
                {{ __('Email Password Reset Link') }}
            </x-admin.button>
        </div>
    </form>
</x-admin.layouts.guest>
