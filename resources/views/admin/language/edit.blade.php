<x-auth-layout title="Edit Language">
    <x-slot:header>Edit Language</x-slot:header>

    <x-card>
        <x-slot:header>
            <x-slot:title>Language Information</x-slot:title>
            <x-slot:description>Update the language name and code.</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            <form method="POST" action="{{ route('admin.update.language', $language) }}" novalidate>
                @csrf
                @method('PUT')

                <div>
                    <x-label for="name" value="Language Name" />
                    <x-input id="name" name="name" type="text" class="mt-1 block w-full"
                             value="{{ old('name', $language->name) }}" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="mt-6">
                    <x-label for="code" value="Language Code" />
                    <x-input id="code" name="code" type="text" class="mt-1 block w-full"
                             value="{{ old('code', $language->code) }}" required />
                    <x-input-error class="mt-2" :messages="$errors->get('code')" />
                </div>

                <div class="mt-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="main" value="1" class="form-checkbox" {{ old('main', $language->main) ? 'checked' : '' }}>
                        <span class="ml-2">Set as Main Language</span>
                    </label>
                </div>

                <div class="mt-8 flex items-center gap-4">
                    <x-button>Update Language</x-button>
                    <x-action-message status="language-updated" />
                </div>
            </form>
        </x-slot:content>
    </x-card>
</x-auth-layout>
