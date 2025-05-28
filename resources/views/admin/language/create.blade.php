<x-auth-layout title="Create Language">
    <x-slot:header>
        Create a New Language
    </x-slot:header>

    <x-card>
        <x-slot:header>
            <x-slot:title>Language Information</x-slot:title>
            <x-slot:description>Define the language name and code (e.g., en, fr).</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            <form method="POST" action="{{ route('admin.languages.store') }}">
                @csrf

                <div>
                    <x-label for="name" value="Language Name" />
                    <x-input class="mt-1 block w-full" id="name" name="name" type="text" value="{{ old('name') }}" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="mt-6">
                    <x-label for="code" value="Language Code" />
                    <x-input class="mt-1 block w-full" id="code" name="code" type="text" value="{{ old('code') }}" required />
                    <x-input-error class="mt-2" :messages="$errors->get('code')" />
                </div>

                <div class="mt-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="main" value="1" class="form-checkbox" {{ old('main') ? 'checked' : '' }}>
                        <span class="ml-2">Set as Main Language</span>
                    </label>
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <x-button>Create Language</x-button>
                    <x-action-message status="language-created" />
                </div>
            </form>
        </x-slot:content>
    </x-card>
</x-auth-layout>
