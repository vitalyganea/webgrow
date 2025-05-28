<x-auth-layout title="Edit Page">
    <x-slot:header>
        Edit Page
    </x-slot:header>

    <x-card>
        <x-slot:header>
            <x-slot:title>Page Information</x-slot:title>
            <x-slot:description>Edit the page title, slug, and content for each language.</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            @if ($languages->isEmpty())
                <p class="text-red-500">No languages available. Please configure languages to edit pages.</p>
            @else
                <div x-data="{ tab: '{{ $languages->first()->code ?? '' }}' }" x-cloak class="space-y-6">
                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200">
                        @foreach ($languages as $language)
                            <button
                                @click="tab = '{{ $language->code }}'"
                                :class="tab === '{{ $language->code }}' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                                class="px-4 py-2 focus:outline-none"
                            >
                                {{ strtoupper($language->code) }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Tab contents -->
                    <form method="POST" action="{{ route('admin.update.page', $group_id) }}">
                        @csrf
                        @method('PUT')

                        @foreach ($languages as $language)
                            @php
                                $page = $pages[$language->code] ?? null;
                            @endphp

                            <div x-show="tab === '{{ $language->code }}'" class="space-y-6">
                                <h3 class="text-lg font-semibold">{{ strtoupper($language->code) }} Content</h3>

                                <input type="hidden" name="pages[{{ $language->code }}][language_code]" value="{{ $language->code }}">

                                <div>
                                    <x-label for="title_{{ $language->code }}" value="Title" />
                                    <x-input
                                        class="mt-1 block w-full"
                                        id="title_{{ $language->code }}"
                                        name="pages[{{ $language->code }}][title]"
                                        type="text"
                                        value="{{ old('pages.' . $language->code . '.title', $page?->title) }}"
                                        required
                                    />
                                    <x-input-error class="mt-2" :messages="$errors->get('pages.' . $language->code . '.title')" />
                                </div>

                                <div class="mt-6">
                                    <x-label for="slug_{{ $language->code }}" value="Slug (URL)" />
                                    <x-input
                                        class="mt-1 block w-full"
                                        id="slug_{{ $language->code }}"
                                        name="pages[{{ $language->code }}][slug]"
                                        type="text"
                                        value="{{ old('pages.' . $language->code . '.slug', $page?->slug) }}"
                                        required
                                    />
                                    <x-input-error class="mt-2" :messages="$errors->get('pages.' . $language->code . '.slug')" />
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-6 flex items-center gap-4">
                            <x-button>Save Changes</x-button>
                        </div>
                    </form>
                </div>
            @endif
        </x-slot:content>
    </x-card>
</x-auth-layout>
