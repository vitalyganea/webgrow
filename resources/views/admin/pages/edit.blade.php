<x-auth-layout title="Edit Page">
    <x-slot:header>
        Edit Page
    </x-slot:header>

    <x-card>
        <x-slot:header>
            <x-slot:title>Page Information</x-slot:title>
            <x-slot:description>Update the page title, slug, and content.</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            <form method="POST" action="{{ route('update.page', $page) }}">
                @csrf
                @method('PUT')

                <div>
                    <x-label for="title" value="Title" />
                    <x-input class="mt-1 block w-full" id="title" name="title" type="text"
                             value="{{ old('title', $page->title) }}" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                </div>

                <div class="mt-6">
                    <x-label for="slug" value="Slug (URL)" />
                    <x-input class="mt-1 block w-full" id="slug" name="slug" type="text"
                             value="{{ old('slug', $page->slug) }}" required />
                    <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                </div>

                <div class="mt-6">
                    <x-label for="content" value="Content" />
                    <textarea name="content" id="content" rows="6"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('content', $page->content) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('content')" />
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <x-button>Update Page</x-button>
                    <x-action-message status="page-updated" />
                </div>
            </form>
        </x-slot:content>
    </x-card>
</x-auth-layout>
