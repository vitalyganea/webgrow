<x-admin.layouts.auth title="Create SEO Tag">
    <x-slot:header>
        Create a New SEO Tag
    </x-slot:header>

    <x-admin.card>
        <x-slot:header>
            <x-slot:title>SEO Tag Information</x-slot:title>
            <x-slot:description>Define your SEO tag (e.g. keywords, meta tag).</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            <form method="POST" action="{{ route('admin.store.seo-tag') }}">
                @csrf

                <div>
                    <x-admin.label for="seo_tag" value="SEO Tag" />
                    <x-admin.input
                        id="seo_tag"
                        name="seo_tag"
                        type="text"
                        class="mt-1 block w-full"
                        value="{{ old('seo_tag') }}"
                        required
                    />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('seo_tag')" />
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <x-admin.button>Create SEO Tag</x-admin.button>
                    <x-admin.action-message status="seo-tag-created" />
                </div>
            </form>
        </x-slot:content>
    </x-admin.card>
</x-admin.layouts.auth>
