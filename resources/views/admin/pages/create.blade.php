<x-auth-layout title="Create Page">
    <x-slot:header>
        Create a New Page
    </x-slot:header>

        <x-card>
            <x-slot:header>
                <x-slot:title>Page Information</x-slot:title>
                <x-slot:description>Define the page title, slug, and content.</x-slot:description>
            </x-slot:header>

            <x-slot:content>
                <form method="POST" action="{{ route('admin.store.page') }}">
                    @csrf

                    <div>
                        <x-label for="title" value="Title" />
                        <x-input class="mt-1 block w-full" id="title" name="title" type="text" value="{{ old('title') }}" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div class="mt-6">
                        <x-label for="slug" value="Slug (URL)" />
                        <x-input class="mt-1 block w-full" id="slug" name="slug" type="text" value="{{ old('slug') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                    </div>

                    <div class="mt-6 flex items-center gap-4">
                        <x-button>Create Page</x-button>
                        <x-action-message status="page-created" />
                    </div>
                </form>
            </x-slot:content>
        </x-card>
</x-auth-layout>
