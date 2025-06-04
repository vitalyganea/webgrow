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

                <div class="mt-6 flex items-center gap-4">
                    <x-admin.button>Update SEO Tag</x-admin.button>
                    <x-admin.action-message status="seo-tag-updated" />
                </div>
            </form>
        </x-slot:content>
    </x-admin.card>
</x-admin.layouts.auth>
