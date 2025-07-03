<x-admin.layouts.auth title="Edit SEO Tag">
    <x-slot:header>
        Edit SEO Tag
    </x-slot:header>

    <x-admin.card>
        <x-slot:header>
            <x-slot:title>SEO Tag Information</x-slot:title>
            <x-slot:description>Update the SEO tag content.</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            <form method="POST" action="{{ route('admin.update.seo-tag', $seoTag) }}">
                @csrf
                @method('PUT')

                <div>
                    <x-admin.label for="seo_tag" value="SEO Tag" />
                    <x-admin.input
                        id="seo_tag"
                        name="seo_tag"
                        type="text"
                        class="mt-1 block w-full"
                        value="{{ old('seo_tag', $seoTag->seo_tag) }}"
                        required
                    />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('seo_tag')" />
                </div>

                <div class="mt-4">
                    <x-admin.label for="type" value="SEO Tag Type" />
                    <select
                        id="type"
                        name="type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required
                    >
                        <option value="">Select Type</option>
                        <option value="text" {{ old('type', $seoTag->type) == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="image" {{ old('type', $seoTag->type) == 'image' ? 'selected' : '' }}>Image</option>
                    </select>
                    <x-admin.input-error class="mt-2" :messages="$errors->get('type')" />
                </div>

                <div class="mt-4">
                    <x-admin.label for="tag_format" value="SEO Tag format" />
                    <x-admin.input
                        id="tag_format"
                        name="tag_format"
                        type="text"
                        class="mt-1 block w-full"
                        value="{{ old('tag_format', $seoTag->tag_format) }}"
                        required
                    />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('tag_format')" />
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <x-admin.button>Update SEO Tag</x-admin.button>
                    <x-admin.action-message status="seo-tag-updated" />
                </div>
            </form>
        </x-slot:content>
    </x-admin.card>
</x-admin.layouts.auth>
