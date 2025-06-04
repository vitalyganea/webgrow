<x-admin.layouts.auth title="Create Page">
    <x-slot:header>
        Create a New Page
    </x-slot:header>

    <x-admin.card>
        <x-slot:header>
            <x-slot:title>Page Information</x-slot:title>
            <x-slot:description>Define the page title, slug, and content.</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            <form method="POST" action="{{ route('admin.store.page') }}">
                @csrf

                <div>
                    <x-admin.label for="title" value="Title" />
                    <x-admin.input class="mt-1 block w-full" id="title" name="title" type="text" value="{{ old('title') }}" required autofocus />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('title')" />
                </div>

                <div class="mt-6">
                    <x-admin.label for="slug" value="Slug (URL)" />
                    <x-admin.input class="mt-1 block w-full" id="slug" name="slug" type="text" value="{{ old('slug') }}" required />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('slug')" />
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <x-admin.button>Create Page</x-admin.button>
                    <x-admin.action-message status="page-created" />
                </div>
            </form>
        </x-slot:content>
    </x-admin.card>
</x-admin.layouts.auth>
